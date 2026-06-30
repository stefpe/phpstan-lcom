<?php

declare(strict_types=1);

namespace D435345\PHPStanLcom\Tests\Rules;

use D435345\PHPStanLcom\Rules\MaxLcom4Rule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<MaxLcom4Rule>
 */
class MaxLcom4RuleTest extends RuleTestCase
{
    private int $maxLcom4 = 2;

    protected function getRule(): Rule
    {
        return new MaxLcom4Rule($this->maxLcom4);
    }


    public function testHighCohesionClass(): void
    {
        $this->maxLcom4 = 2;
        $this->analyse(
            [__DIR__ . '/Fixture/ClassWithHighCohesion.php'],
            [],
        );
    }

    public function testLowCohesionClass(): void
    {
        $this->maxLcom4 = 2;
        $this->analyse(
            [__DIR__ . '/Fixture/ClassWithLowCohesion.php'],
            [
                [
                    'Class D435345\PHPStanLcom\Tests\Rules\Fixture\ClassWithLowCohesion has LCOM4 value of 3. Max allowed is 2. Consider refactoring!',
                    5,
                ],
            ],
        );
    }

    public function testLowCohesionClassWithHigherThreshold(): void
    {
        $this->maxLcom4 = 4;
        $this->analyse(
            [__DIR__ . '/Fixture/ClassWithLowCohesion.php'],
            [],
        );
    }

    public function testClassWithOnlyGettersSetters(): void
    {
        $this->maxLcom4 = 2;
        $this->analyse(
            [__DIR__ . '/Fixture/ClassWithOnlyGettersSetters.php'],
            [],
        );
    }

    public function testClassWithCallsCohesion(): void
    {
        $this->maxLcom4 = 2;
        $this->analyse(
            [__DIR__ . '/Fixture/ClassWithCallsCohesion.php'],
            [],
        );
    }
}
