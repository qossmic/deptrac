<?php

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Exception\Console\InvalidArgumentException;
use function is_string;

class DebugLayerOptions
{
    private string $configurationFile;

    private ?string $layer;

    /**
     * @param mixed $configurationFile
     */
    public function __construct($configurationFile, ?string $layer)
    {
        if (!is_string($configurationFile)) {
            throw InvalidArgumentException::invalidDepfileType($configurationFile);
        }

        $this->configurationFile = $configurationFile;
        $this->layer = $layer;
    }

    public function getConfigurationFile(): string
    {
        return $this->configurationFile;
    }

    public function getLayer(): ?string
    {
        return $this->layer;
    }
}
