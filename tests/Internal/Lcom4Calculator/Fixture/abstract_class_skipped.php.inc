<?php

namespace TestFixtures;

abstract class AbstractClass
{
    private string $value;

    abstract public function doSomething(): void;

    public function getValue(): string
    {
        return $this->value;
    }
}
