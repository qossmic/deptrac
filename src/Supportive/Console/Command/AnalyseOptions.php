<?php

namespace Qossmic\Deptrac\Supportive\Console\Command;

class AnalyseOptions
{
    public function __construct(private bool $noProgress, private string $formatter, private ?string $output, private bool $reportSkipped, private bool $reportUncovered, private bool $failOnUncovered)
    {
    }

    public function showProgress(): bool
    {
        return !$this->noProgress;
    }

    public function getFormatter(): string
    {
        return $this->formatter;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function reportSkipped(): bool
    {
        return $this->reportSkipped;
    }

    public function reportUncovered(): bool
    {
        return $this->reportUncovered;
    }

    public function failOnUncovered(): bool
    {
        return $this->failOnUncovered;
    }
}
