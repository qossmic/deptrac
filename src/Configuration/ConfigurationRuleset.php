<?php

namespace SensioLabs\Deptrac\Configuration;

class ConfigurationRuleset
{
    private $layerMap = [];

    public static function fromArray(array $arr): self
    {
        return new static($arr);
    }

    /**
     * @param string[] $layerMap
     */
    private function __construct(array $layerMap)
    {
        $this->layerMap = $layerMap;
    }

    /**
     * @param string $layerName
     * @return string[]
     */
    public function getAllowedDependencies(string $layerName): array
    {
        return $this->layerMap[$layerName] ?? [];
    }
}
