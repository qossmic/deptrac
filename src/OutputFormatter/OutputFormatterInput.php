<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

class OutputFormatterInput
{
    private ?string $outputPath;

    private bool $reportSkipped;

    private bool $reportUncovered;

    private bool $failOnUncovered;

    public function __construct(?string $outputPath, bool $reportSkipped, bool $reportUncovered, bool $failOnUncovered)
    {
        $this->outputPath = $outputPath;
        $this->reportSkipped = $reportSkipped;
        $this->reportUncovered = $reportUncovered;
        $this->failOnUncovered = $failOnUncovered;
    }

    public function getOutputPath(): ?string
    {
        return $this->outputPath;
    }

    public function getReportSkipped(): bool
    {
        return $this->reportSkipped;
    }

    public function getReportUncovered(): bool
    {
        return $this->reportUncovered;
    }

    public function getFailOnUncovered(): bool
    {
        return $this->failOnUncovered;
    }
}
