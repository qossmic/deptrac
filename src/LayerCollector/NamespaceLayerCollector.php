<?php

# Beste Lösung für den Collector
# Der Collector bekommt die klasse (AST) sowie alle Klassen (AST) von denen es genutzt wurde.
# Im RAM liegt eine Map aus allen AST's



namespace DependencyTracker\LayerCollector;

use DependencyTracker\CollectionMap;

class NamespaceLayerCollector implements LayerCollectorInterface
{
    protected $config;

    protected $layerName;

    /**
     * NamespaceLayerCollector constructor.
     * @param $config
     */
    public function __construct($layerName, $config)
    {
        $this->config = $config;
        $this->layerName = $layerName;
    }

    public function supports($klass, $depdendency)
    {
        if ((stripos($klass, 'test') || stripos($depdendency, 'test'))) {
            return false;
        }

        return true;
    }

    public function handle(CollectionMap $map, $klass, $depdendency)
    {
        $klass = $this->normalize($klass);
        $depdendency = $this->normalize($depdendency);

        if (!$klass || !$depdendency) {
            return;
        }

        $map->add($klass, $depdendency);
    }

    public function getColor()
    {
        // TODO: Implement getColor() method.
    }

}
