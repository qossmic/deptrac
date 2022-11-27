<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config\Formatter;

use Qossmic\Deptrac\Contract\Config\LayerConfig;

final class GraphvizConfig implements FormatterConfigInterface
{
    private string $name = 'graphviz';
    private bool $pointsToGroup = false;

    /** @var LayerConfig[] */
    private array $hiddenLayers = [];

    /** @var array<string, LayerConfig[]> */
    private array $groups = [];

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function pointsToGroup(bool $pointsToGroup = true): self
    {
        $this->pointsToGroup = $pointsToGroup;

        return $this;
    }

    public function hiddenLayers(LayerConfig ...$LayerConfigs): self
    {
        foreach ($LayerConfigs as $layerConfig) {
            $this->hiddenLayers[] = $layerConfig;
        }

        return $this;
    }

    public function groups(string $name, LayerConfig ...$layerConfigs): self
    {
        foreach ($layerConfigs as $layerConfig) {
            $this->groups[$name][] = $layerConfig;
        }

        return $this;
    }

    public function toArray(): array
    {
        $output = [];

        if ([] !== $this->hiddenLayers) {
            $output['hidden_layers'] = array_map(static fn (LayerConfig $config) => $config->name, $this->hiddenLayers);
        }

        if ([] !== $this->groups) {
            $output['groups'] = array_map(
                static fn (array $configs) => array_map(static fn (LayerConfig $layer) => $layer->name, $configs),
                $this->groups
            );
        }

        $output['point_to_groups'] = $this->pointsToGroup;

        return $output;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
