<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\OutputFormatter\Configuration;

final class ConfigurationGraphViz
{
    /**
     * @param array{hidden_layers: string[], groups: array<string, string[]>, point_to_groups: bool} $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr['hidden_layers'], $arr['groups'], $arr['point_to_groups']);
    }

    /**
     * @param string[]                $hiddenLayers
     * @param array<string, string[]> $groupsLayerMap
     */
    private function __construct(private readonly array $hiddenLayers, private readonly array $groupsLayerMap, private readonly bool $pointToGroups)
    {
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

    public function getPointToGroups(): bool
    {
        return $this->pointToGroups;
    }
}
