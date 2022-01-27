<?php

namespace Qossmic\Deptrac\Console\Command;

class DebugLayerOptions
{
    private string $configurationFile;

    private ?string $layer;

    public function __construct(string $configurationFile, ?string $layer)
    {
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
