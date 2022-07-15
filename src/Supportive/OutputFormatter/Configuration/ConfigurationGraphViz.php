<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\OutputFormatter\Configuration;

final class ConfigurationGraphViz
{
    /** @var array<string, string[]> */
    private array $groupsLayerMap;

    /** @var string[] */
    private array $hiddenLayers;

    private bool $pointToGroups;

    /**
     * @param array{hidden_layers?: string[], groups?: array<string, string[]>, pointToGroups?: bool} $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr['hidden_layers'] ?? [], $arr['groups'] ?? [], $arr['pointToGroups'] ?? false);
    }

    /**
     * @param string[]                $hiddenLayers
     * @param array<string, string[]> $groupsLayerMap
     */
    private function __construct(array $hiddenLayers, array $groupsLayerMap, bool $pointToGroups)
    {
        $this->groupsLayerMap = $groupsLayerMap;
        $this->hiddenLayers = $hiddenLayers;
        $this->pointToGroups = $pointToGroups;
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
