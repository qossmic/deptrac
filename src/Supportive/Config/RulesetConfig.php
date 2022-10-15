<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Config;

final class RulesetConfig
{
    /** @var array<LayerConfig> */
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

    /** @return array<string, string> */
    public function toArray(): array
    {
        /** @var array<string> */
        $data = array_map(static fn (LayerConfig $layerConfig) => $layerConfig->__toString(), $this->accessableLayers);

        return $data + ['name' => $this->layersConfig->__toString()];
    }
}
