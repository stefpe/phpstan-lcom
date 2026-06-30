<?php

namespace TestFixtures;

class MethodCallsConnection
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

    public function cleanup(): void
    {
        $this->data = '';
    }
}
