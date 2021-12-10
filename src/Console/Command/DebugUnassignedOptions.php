<?php

namespace Qossmic\Deptrac\Console\Command;

use function is_string;
use Qossmic\Deptrac\Exception\Console\InvalidArgumentException;

class DebugUnassignedOptions
{
    private string $configurationFile;

    /**
     * @param mixed $configurationFile
     */
    public function __construct($configurationFile)
    {
        if (!is_string($configurationFile)) {
            throw InvalidArgumentException::invalidDepfileType($configurationFile);
        }

        $this->configurationFile = $configurationFile;
    }

    public function getConfigurationFile(): string
    {
        return $this->configurationFile;
    }
}
