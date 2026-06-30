<?php

namespace TestFixtures;

class CohesiveClass
{
    private string $name;
    private int $age;

    public function display(): string
    {
        return $this->name . ' is ' . $this->age;
    }

    public function update(string $name, int $age): void
    {
        $this->name = $name;
        $this->age = $age;
    }
}
