<?php

namespace D435345\PHPStanLcom\Tests\Rules\Fixture;

class ClassWithOnlyGettersSetters
{
    private string $foo;
    private string $bar;

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function setFoo(string $foo): void
    {
        $this->foo = $foo;
    }

    public function getBar(): string
    {
        return $this->bar;
    }

    public function setBar(string $bar): void
    {
        $this->bar = $bar;
    }
}
