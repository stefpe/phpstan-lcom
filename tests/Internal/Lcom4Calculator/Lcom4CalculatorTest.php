<?php

declare(strict_types=1);

namespace D435345\PHPStanLcom\Tests\Internal\Lcom4Calculator;

use D435345\PHPStanLcom\Internal\Lcom4Calculator;
use Iterator;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class Lcom4CalculatorTest extends TestCase
{
    private Lcom4Calculator $lcom4Calculator;

    protected function setUp(): void
    {
        $this->lcom4Calculator = new Lcom4Calculator();
    }

    #[DataProvider('provideFixtureFiles')]
    public function test(string $filePath, int $expectedLcom4): void
    {
        $classNode = $this->parseFileToFirstClass($filePath);
        $lcom4 = $this->lcom4Calculator->calculate($classNode);

        $this->assertSame($expectedLcom4, $lcom4);
    }

    public static function provideFixtureFiles(): Iterator
    {
        $fixtureDir = __DIR__ . '/Fixture';

        foreach (glob($fixtureDir . '/*.php.inc') as $filePath) {
            $filename = basename($filePath);

            if (preg_match('/_lcom4_(\d+)\.php\.inc$/', $filename, $matches)) {
                $expectedLcom4 = (int) $matches[1];

                if (str_contains($filename, 'abstract_class')) {
                    yield $filename => [$filePath, 1];
                    continue;
                }

                yield $filename => [$filePath, $expectedLcom4];
            }
        }
    }

    private function parseFileToFirstClass(string $filePath): Class_
    {
        $fileContents = file_get_contents($filePath);
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->createForNewestSupportedVersion();
        $nodes = $parser->parse($fileContents);

        $nodeFinder = new NodeFinder();
        $firstClass = $nodeFinder->findFirstInstanceOf((array) $nodes, Class_::class);

        if (!$firstClass instanceof Class_) {
            throw new \RuntimeException('No class found in ' . $filePath);
        }

        return $firstClass;
    }
}
