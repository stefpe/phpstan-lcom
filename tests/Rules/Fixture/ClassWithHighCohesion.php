<?php

namespace D435345\PHPStanLcom\Tests\Rules\Fixture;

class ClassWithHighCohesion
{
    private string $name;
    private int $age;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function display(): string
    {
        return $this->name . ' is ' . $this->age;
    }
}
