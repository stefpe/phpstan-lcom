<?php

namespace TestFixtures;

class ThreeGroups
{
    private array $emailConfig;
    private array $logEntries;
    private string $reportPath;

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

    public function generate(): string
    {
        return 'Report at: ' . $this->reportPath;
    }
}
