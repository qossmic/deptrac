<?php

namespace DependencyTracker;


use DependencyTracker\LayerCollector\LayerCollectorInterface;

class CollectionMap
{
    protected $depdendencyMap = [];

    /** @var LayerCollectorInterface[] */
    protected $layers = [];

    /**
     * CollectionMap constructor.
     * @param array $layerCollectors
     */
    public function __construct(array $layers)
    {
        $this->layers = $layers;
    }

    public function addDependency($klass, $depdendency)
    {
        foreach ($this->layers as $layer) {
            foreach ($layer->getCollectors() as $layerCollector) {
                if (!$layerCollector->supports($klass, $depdendency)) {
                    continue;
                }

                $layerCollector->handle($this, $klass, $depdendency);
                break;
            }
        }
    }

    public function add($layerFrom, $layerTo)
    {
        if(!isset($this->depdendencyMap[$layerFrom])) {
            $this->depdendencyMap[$layerFrom] = [];
        }

        if(!isset($this->depdendencyMap[$layerFrom][$layerTo])) {
            $this->depdendencyMap[$layerFrom][$layerTo] = [
                'count' => 0
            ];
        }

        $this->depdendencyMap[$layerFrom][$layerTo]['count']++;
    }

    public function getDependencies()
    {
        return $this->depdendencyMap;
    }
} 