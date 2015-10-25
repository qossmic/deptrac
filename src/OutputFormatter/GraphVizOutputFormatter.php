<?php

namespace DependencyTracker\OutputFormatter;

use DependencyTracker\ClassLayerMap;
use DependencyTracker\Event\Visitor\FoundDependencyEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GraphVizOutputFormatter
{
    protected $eventDispatcher;

    protected $classLayerMap;

    protected $layersDependOnLayers = [];


    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ClassLayerMap $classLayerMap
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->classLayerMap = $classLayerMap;
        $eventDispatcher->addListener(FoundDependencyEvent::class, [$this, 'onFoundDepdendencyEvent']);
    }

    public function onFoundDepdendencyEvent(FoundDependencyEvent $dependencyEvent)
    {
        $layersA = $this->classLayerMap->getLayersByClassName($dependencyEvent->getClassA());
        $layersB = $this->classLayerMap->getLayersByClassName($dependencyEvent->getClassB());

        foreach ($layersA as $layerA) {

            if (!isset($this->layersDependOnLayers[$layerA])) {
                $this->layersDependOnLayers[$layerA] = [];
            }

            $this->layersDependOnLayers[$layerA] = array_values(array_unique(array_merge($layersB, $this->layersDependOnLayers[$layerA])));
        }
    }

    public function finish()
    {
        $graph = new \Fhaculty\Graph\Graph();
        $vertices = [];

        // create a vertice for every layer
        foreach ($this->layersDependOnLayers as $layer => $layersDependOn) {

            if (!isset($vertices[$layer])) {
                $vertices[$layer] = $graph->createVertex($layer);
            }

            foreach($layersDependOn as $layerDependOn) {
                if (!isset($vertices[$layerDependOn])) {
                    $vertices[$layerDependOn] = $graph->createVertex($layerDependOn);
                }
            }

        }

        // createEdges
        foreach ($this->layersDependOnLayers as $layer => $layersDependOn) {
            foreach ($layersDependOn as $layerDependOn) {
                $vertices[$layer]->createEdgeTo($vertices[$layerDependOn]);
            }
        }

        $graphviz = new \Graphp\GraphViz\GraphViz();
        $graphviz->display($graph);
    }

}
