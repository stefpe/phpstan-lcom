<?php

namespace D435345\PHPStanLcom\Tests\Rules\Fixture;

class ClassWithCallsCohesion
{
    private string $data;

    public function process(): void
    {
        $this->validate();
        $this->save();
    }

    public function validate(): void
    {
        $this->data = 'validated';
    }

    public function save(): void
    {
        $this->data = 'saved';
    }

    public function unrelated(): void
    {
        echo 'hello';
    }
}
