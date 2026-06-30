<?php

declare(strict_types=1);

namespace D435345\PHPStanLcom\Rules;

use D435345\PHPStanLcom\Internal\Lcom4Calculator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * @implements Rule<Class_>
 */
class MaxLcom4Rule implements Rule
{
    private int $maxLcom4;

    public function __construct(int $maxLcom4 = 2)
    {
        $this->maxLcom4 = $maxLcom4;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name === null) {
            return [];
        }

        if ($node->isAbstract()) {
            return [];
        }

        if ($node->namespacedName === null) {
            throw new ShouldNotHappenException('Class without namespaced name');
        }

        $className = $node->namespacedName->toString();

        $lcom4 = (new Lcom4Calculator())->calculate($node);

        if ($lcom4 > $this->maxLcom4) {
            return [
                RuleErrorBuilder::message(
                    sprintf(
                        'Class %s has LCOM4 value of %d. Max allowed is %d. Consider refactoring!',
                        $className,
                        $lcom4,
                        $this->maxLcom4
                    )
                )->identifier('phpstanLcom.maxLcom4')
                    ->build(),
            ];
        }

        return [];
    }
}
