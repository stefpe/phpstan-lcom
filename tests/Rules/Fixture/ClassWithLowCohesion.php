<?php

namespace D435345\PHPStanLcom\Tests\Rules\Fixture;

class ClassWithLowCohesion
{
    private array $emailConfig;
    private array $logEntries;
    private string $reportPath;

    public function sendEmail(string $to, string $subject): void
    {
        $this->emailConfig['to'] = $to;
        $this->emailConfig['subject'] = $subject;
        $this->emailConfig['sent'] = true;
    }

    public function validateEmail(): bool
    {
        return isset($this->emailConfig['to']);
    }

    public function addLogEntry(string $message): array
    {
        $this->logEntries[] = $message;
        return $this->logEntries;
    }

    public function countLogEntries(): int
    {
        return count($this->logEntries);
    }

    public function generateReport(): string
    {
        return 'Report at: ' . $this->reportPath;
    }

    public function setReportPath(string $path): void
    {
        $this->reportPath = $path;
    }
}
