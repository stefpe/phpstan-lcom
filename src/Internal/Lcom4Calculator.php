<?php

declare(strict_types=1);

namespace D435345\PHPStanLcom\Internal;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;

final class Lcom4Calculator
{
    private const MAGIC_METHODS = [
        '__construct', '__destruct', '__call', '__callStatic',
        '__get', '__set', '__isset', '__unset',
        '__sleep', '__wakeup', '__toString',
        '__invoke', '__set_state', '__clone', '__debugInfo',
    ];

    public function calculate(Class_ $classNode): int
    {
        $methods = $this->getRelevantMethods($classNode);

        $methodCount = count($methods);
        if ($methodCount <= 1) {
            return 1;
        }

        $methodNames = array_keys($methods);
        $adjacencyList = $this->buildAdjacencyList($methods);

        return $this->countConnectedComponents($methodNames, $adjacencyList);
    }

    /**
     * @return array<string, ClassMethod>
     */
    private function getRelevantMethods(Class_ $classNode): array
    {
        $methods = [];
        foreach ($classNode->getMethods() as $method) {
            if ($method->isAbstract()) {
                continue;
            }

            $methodName = (string) $method->name;
            if (in_array($methodName, self::MAGIC_METHODS, true)) {
                continue;
            }

            if ($this->isGetterOrSetter($method)) {
                continue;
            }

            $methods[$methodName] = $method;
        }

        return $methods;
    }

    private function isGetterOrSetter(ClassMethod $method): bool
    {
        $stmts = $method->stmts;
        if ($stmts === null || count($stmts) !== 1) {
            return false;
        }

        $stmt = $stmts[0];

        if ($stmt instanceof Return_) {
            if ($stmt->expr instanceof PropertyFetch) {
                return $this->isThisPropertyFetch($stmt->expr);
            }
        }

        if ($stmt instanceof Expression) {
            if ($stmt->expr instanceof Assign) {
                $assign = $stmt->expr;
                if ($assign->var instanceof PropertyFetch) {
                    return $this->isThisPropertyFetch($assign->var);
                }
            }
        }

        return false;
    }

    private function isThisPropertyFetch(PropertyFetch $fetch): bool
    {
        return $fetch->var instanceof Variable
            && $fetch->var->name === 'this'
            && $fetch->name instanceof Identifier;
    }

    /**
     * @param array<string, ClassMethod> $methods
     * @return array<string, list<string>>
     */
    private function buildAdjacencyList(array $methods): array
    {
        $nodeFinder = new NodeFinder();

        $methodPropertyAccess = [];
        $methodCalls = [];

        foreach ($methods as $methodName => $method) {
            $stmts = $method->stmts;
            if ($stmts === null) {
                continue;
            }

            $propertyFetches = $nodeFinder->find($stmts, function (Node $node) {
                return $node instanceof PropertyFetch
                    && $node->var instanceof Variable
                    && $node->var->name === 'this';
            });

            $propertiesPerMethod = [];
            /** @var PropertyFetch $fetch */
            foreach ($propertyFetches as $fetch) {
                $propName = $fetch->name instanceof Identifier
                    ? $fetch->name->toString()
                    : null;
                if ($propName !== null) {
                    $propertiesPerMethod[$propName] = true;
                }
            }
            $methodPropertyAccess[$methodName] = array_keys($propertiesPerMethod);

            $foundCalls = $nodeFinder->find($stmts, function (Node $node) {
                return $node instanceof MethodCall
                    && $node->var instanceof Variable
                    && $node->var->name === 'this';
            });

            $calls = [];
            /** @var MethodCall $call */
            foreach ($foundCalls as $call) {
                $calledMethod = $call->name instanceof Identifier
                    ? $call->name->toString()
                    : null;
                if ($calledMethod !== null && isset($methods[$calledMethod])) {
                    $calls[] = $calledMethod;
                }
            }
            $methodCalls[$methodName] = $calls;
        }

        $methodNames = array_keys($methods);
        $adjacencyList = [];
        foreach ($methodNames as $name) {
            $adjacencyList[$name] = [];
        }

        for ($i = 0; $i < count($methodNames); $i++) {
            for ($j = $i + 1; $j < count($methodNames); $j++) {
                $a = $methodNames[$i];
                $b = $methodNames[$j];

                if ($this->areMethodsConnected(
                    $a, $b,
                    $methodPropertyAccess,
                    $methodCalls,
                )) {
                    $adjacencyList[$a][] = $b;
                    $adjacencyList[$b][] = $a;
                }
            }
        }

        return $adjacencyList;
    }

    /**
     * @param array<string, list<string>> $methodPropertyAccess
     * @param array<string, list<string>> $methodCalls
     */
    private function areMethodsConnected(
        string $methodA,
        string $methodB,
        array $methodPropertyAccess,
        array $methodCalls,
    ): bool {
        $propsA = $methodPropertyAccess[$methodA] ?? [];
        $propsB = $methodPropertyAccess[$methodB] ?? [];

        foreach ($propsA as $prop) {
            if (in_array($prop, $propsB, true)) {
                return true;
            }
        }

        if (in_array($methodB, $methodCalls[$methodA] ?? [], true)) {
            return true;
        }

        if (in_array($methodA, $methodCalls[$methodB] ?? [], true)) {
            return true;
        }

        return false;
    }

    /**
     * @param list<string> $methodNames
     * @param array<string, list<string>> $adjacencyList
     */
    private function countConnectedComponents(array $methodNames, array $adjacencyList): int
    {
        $visited = [];
        foreach ($methodNames as $name) {
            $visited[$name] = false;
        }

        $components = 0;

        foreach ($methodNames as $start) {
            if ($visited[$start]) {
                continue;
            }

            $components++;
            $stack = [$start];
            $visited[$start] = true;

            while ($stack !== []) {
                $current = array_pop($stack);
                foreach ($adjacencyList[$current] as $neighbor) {
                    if (!$visited[$neighbor]) {
                        $visited[$neighbor] = true;
                        $stack[] = $neighbor;
                    }
                }
            }
        }

        return $components;
    }
}
