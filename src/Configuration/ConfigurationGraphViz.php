<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

final class ConfigurationGraphViz
{
    /** @var array<string, string[]> */
    private array $groupsLayerMap;

    /** @var string[] */
    private array $hiddenLayers;

    /**
     * @param array{hidden_layers?: string[], groups?: array<string, string[]>} $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr['hidden_layers'] ?? [], $arr['groups'] ?? []);
    }

    /**
     * @param string[]                $hiddenLayers
     * @param array<string, string[]> $groupsLayerMap
     */
    private function __construct(array $hiddenLayers, array $groupsLayerMap)
    {
        $this->groupsLayerMap = $groupsLayerMap;
        $this->hiddenLayers = $hiddenLayers;
    }

    /**
     * @return array<string, string[]>
     */
    public function getGroupsLayerMap(): array
    {
        return $this->groupsLayerMap;
    }

    /**
     * @return string[]
     */
    public function getHiddenLayers(): array
    {
        return $this->hiddenLayers;
    }
}
