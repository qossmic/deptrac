<?php

namespace Qossmic\Deptrac\Console\Command;

use function is_string;
use Qossmic\Deptrac\Exception\Console\InvalidArgumentException;

class AnalyseOptions
{
    private string $configurationFile;

    private bool $noProgress;

    private string $formatter;

    private bool $failOnUncovered;

    /**
     * @param mixed $configurationFile
     */
    public function __construct($configurationFile, bool $noProgress, string $formatter, bool $failOnUncovered)
    {
        if (!is_string($configurationFile)) {
            throw InvalidArgumentException::invalidDepfileType($configurationFile);
        }

        $this->configurationFile = $configurationFile;
        $this->noProgress = $noProgress;
        $this->formatter = $formatter;
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

    public function getFormatter(): string
    {
        return $this->formatter;
    }

    public function failOnUncovered(): bool
    {
        return $this->failOnUncovered;
    }
}
