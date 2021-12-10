<?php

namespace Qossmic\Deptrac\Console\Command;

use function is_string;
use Qossmic\Deptrac\Exception\Console\InvalidArgumentException;

class AnalyseOptions
{
    private string $configurationFile;

    private bool $noProgress;

    /** @var string[] */
    private array $formatters;

    private bool $failOnUncovered;

    /**
     * @param mixed    $configurationFile
     * @param string[] $formatters
     */
    public function __construct($configurationFile, bool $noProgress, array $formatters, bool $failOnUncovered)
    {
        if (!is_string($configurationFile)) {
            throw InvalidArgumentException::invalidDepfileType($configurationFile);
        }

        $this->configurationFile = $configurationFile;
        $this->noProgress = $noProgress;
        $this->formatters = $formatters;
        $this->failOnUncovered = $failOnUncovered;
    }

    public function getConfigurationFile(): string
    {
        return $this->configurationFile;
    }

    public function showProgress(): bool
    {
        return !$this->noProgress;
    }

    /**
     * @return string[]
     */
    public function getFormatters(): array
    {
        return $this->formatters;
    }

    public function failOnUncovered(): bool
    {
        return $this->failOnUncovered;
    }
}
