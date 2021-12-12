<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

class OutputFormatterInput
{
    /** @var array<string, mixed> */
    private array $config;

    private ?string $outputPath;

    private bool $reportSkipped;

    private bool $reportUncovered;

    private bool $failOnUncovered;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(?string $outputPath, bool $reportSkipped, bool $reportUncovered, bool $failOnUncovered, array $config = [])
    {
        $this->outputPath = $outputPath;
        $this->reportSkipped = $reportSkipped;
        $this->reportUncovered = $reportUncovered;
        $this->failOnUncovered = $failOnUncovered;
        $this->config = $config;
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

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
