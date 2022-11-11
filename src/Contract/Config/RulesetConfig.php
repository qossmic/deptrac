<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

final class RulesetConfig
{
    /** @var array<Layer> */
    private array $accessableLayers = [];

    public function __construct(
        public readonly Layer $layersConfig
    ) {
    }

    public static function layer(Layer $layerConfig): self
    {
        return new self($layerConfig);
    }

    public function accesses(Layer ...$layersConfig): self
    {
        foreach ($layersConfig as $layerConfig) {
            $this->accessableLayers[] = $layerConfig;
        }

        return $this;
    }

    /** @return non-empty-array<array-key, string> */
    public function toArray(): array
    {
        $data = array_map(static fn (Layer $layerConfig) => $layerConfig->name, $this->accessableLayers);

        return $data + ['name' => $this->layersConfig->name];
    }
}
