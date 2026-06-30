<?php

namespace TestFixtures;

class MagicMethods
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return json_encode($this->data);
    }
}
