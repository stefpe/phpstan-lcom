<?php

namespace TestFixtures;

class TwoGroups
{
    private array $emailConfig;
    private array $logEntries;

    public function send(): void
    {
        $this->emailConfig['sent'] = true;
    }

    public function validate(): bool
    {
        return isset($this->emailConfig['to']);
    }

    public function add(string $message): void
    {
        $this->logEntries[] = $message;
    }

    public function count(): int
    {
        return count($this->logEntries);
    }
}
