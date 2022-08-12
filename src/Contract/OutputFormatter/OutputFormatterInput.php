<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\OutputFormatter;

class OutputFormatterInput
{
    public function __construct(private readonly ?string $outputPath, private readonly bool $reportSkipped, private readonly bool $reportUncovered, private readonly bool $failOnUncovered)
    {
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
