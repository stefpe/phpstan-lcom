<?php

namespace TestFixtures;

class SingleMethod
{
    private string $name;

    public function execute(): void
    {
        $this->name = 'done';
    }
}
