<?php 

namespace DependencyTracker\Configuration;

class ConfigurationRuleset
{
    private $layerMap = [];

    /**
     * ConfigurationRuleset constructor.
     * @param array $layerMap
     */
    public function __construct(array $layerMap)
    {
        $this->layerMap = $layerMap;
    }

    /**
     * @return array
     */
    public function getAllowedDependenvies($layerName)
    {
        if (!isset($this->layerMap[$layerName])) {
            return [];
        }

        return $this->layerMap[$layerName];
    }
}
