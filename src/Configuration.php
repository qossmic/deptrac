<?php

namespace DependencyTracker;

use DependencyTracker\LayerCollector\NamespaceLayerCollector;

class Configuration
{
    protected $config;

    protected $configDir;

    public function __construct($config, $configDir)
    {
        $this->config = $config;
        $this->configDir = $configDir;
    }

    public function getLayers()
    {
        $layers = [];

        foreach ($this->config['layers'] as $layerConfig) {
            $collectors = [];

            foreach ($layerConfig['collectors'] as $collectorConfig) {
                if ($collectorConfig['type'] === "NamespaceLayerCollector") {
                    $collectors[] = new NamespaceLayerCollector($collectorConfig);
                }
            }

            $layers[] = new Layer($collectors);
        }

        return $layers;
    }

    public function getDirs()
    {
        if (!isset($this->config['paths'])) {
            return [];
        }

        return array_map(function($path) {
            return $this->configDir.'/'.$path;
        }, $this->config['paths']);
    }

}
