<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Config;

final class RulesetConfig
{
    private array $accessableLayers = [];

    public function __construct(
        private readonly LayerConfig $layersConfig
    ) {
    }

    public function accessesLayer(LayerConfig ...$layersConfig): self
    {

        foreach ($layersConfig as $layerConfig) {
            $this->accessableLayers[] = $layerConfig;
        }

        return $this;
    }

    public function toArray(): array
    {
        $data = ['name' => $this->layersConfig->__toString()];

        foreach ($this->accessableLayers as $accessesLayer) {
            $data[] = $accessesLayer->__toString() ;
        }

        return $data;
    }
}
