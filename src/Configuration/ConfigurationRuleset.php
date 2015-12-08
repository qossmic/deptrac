<?php 

namespace DependencyTracker\Configuration;

class ConfigurationRuleset
{
    private $layerMap = [];

    public static function fromArray(array $arr)
    {
        return new static($arr);
    }

    /**
     * ConfigurationRuleset constructor.
     * @param array $layerMap
     */
    private function __construct(array $layerMap)
    {
        $this->layerMap = $layerMap;
    }

    /**
     * @return array
     */
    public function getAllowedDependendencies($layerName)
    {
        if (!isset($this->layerMap[$layerName])) {
            return [];
        }

        return $this->layerMap[$layerName];
    }
}
