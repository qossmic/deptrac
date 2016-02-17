<?php

namespace DependencyTracker\OutputFormatter;

use DependencyTracker\ClassLayerMap;
use DependencyTracker\ClassNameLayerResolverInterface;
use DependencyTracker\DependencyResult;
use DependencyTracker\RulesetEngine\RulesetViolation;
use Fhaculty\Graph\Vertex;
use SensioLabs\AstRunner\AstMap;

class GraphVizOutputFormatter implements OutputFormatterInterface
{
    protected $eventDispatcher;

    public function getName()
    {
        return 'graphviz';
    }

    /**
     * @param AstMap $astMap
     * @param RulesetViolation[] $violations
     * @param DependencyResult $dependencyResult
     * @param ClassNameLayerResolverInterface $classNameLayerResolver
     */
    public function finish(AstMap $astMap, array $violations, DependencyResult $dependencyResult, ClassNameLayerResolverInterface $classNameLayerResolver)
    {
        $layersDependOnLayers = [];


        $layerViolations = [];
        foreach ($violations as $violation) {
            if (!isset($layerViolations[$violation->getLayerA()])) {
                $layerViolations[$violation->getLayerA()] = [];
            }

            if (!isset($layerViolations[$violation->getLayerA()][$violation->getLayerB()])) {
                $layerViolations[$violation->getLayerA()][$violation->getLayerB()] = 1;
            } else {
                $layerViolations[$violation->getLayerA()][$violation->getLayerB()] = $layerViolations[$violation->getLayerA()][$violation->getLayerB()] + 1;
            }
        }

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {

            $layersA = $classNameLayerResolver->getLayersByClassName($dependency->getClassA());
            $layersB = $classNameLayerResolver->getLayersByClassName($dependency->getClassB());

            if (empty($layersB)) {
                continue;
            }

            foreach ($layersA as $layerA) {

                if (!isset($layersDependOnLayers[$layerA])) {
                    $layersDependOnLayers[$layerA] = [];
                }

                $layersDependOnLayers[$layerA] = array_values(
                    array_unique(array_merge($layersB, $layersDependOnLayers[$layerA]))
                );
            }
        }

        // refactor to multiple methods

        $graph = new \Fhaculty\Graph\Graph();

        /** @var $vertices Vertex[] */
        $vertices = [];

        // create a vertice for every layer
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {

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
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            foreach ($layersDependOn as $layerDependOn) {

                $vertices[$layer]->createEdgeTo($vertices[$layerDependOn]);
                if (isset($layerViolations[$layer], $layerViolations[$layer][$layerDependOn])) {
                    $edge = $vertices[$layer]->getEdgesTo($vertices[$layerDependOn])->getEdgeFirst();
                    $edge->setAttribute('graphviz.label', $layerViolations[$layer][$layerDependOn]);
                    $edge->setAttribute('graphviz.color', 'red');
                }

            }
        }

        $graphviz = new \Graphp\GraphViz\GraphViz();
        $graphviz->display($graph);
    }

}
