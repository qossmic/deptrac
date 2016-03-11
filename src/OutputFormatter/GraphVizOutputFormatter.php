<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\OutputFormatter\OutputFormatterOption;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Fhaculty\Graph\Vertex;
use SensioLabs\AstRunner\AstMap;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class GraphVizOutputFormatter implements OutputFormatterInterface
{
    protected $eventDispatcher;

    public function getName()
    {
        return 'graphviz';
    }

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions()
    {
        return [
            OutputFormatterOption::newValueOption('foo1', 'gib mir foo1', 'bar1')
        ];
    }


    /**
     * @param AstMap                          $astMap
     * @param RulesetViolation[]              $violations
     * @param DependencyResult                $dependencyResult
     * @param ClassNameLayerResolverInterface $classNameLayerResolver
     * @param OutputInterface                 $output
     */
    public function finish(
        AstMap $astMap,
        array $violations,
        DependencyResult $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver,
        OutputInterface $output
    ) {

        $layerViolations = $this->calculateViolations($violations);

        $layersDependOnLayers = $this->calculateLayerDependencies($astMap, $dependencyResult, $classNameLayerResolver);

        // refactor to multiple methods

        $graph = new \Fhaculty\Graph\Graph();

        /** @var $vertices Vertex[] */
        $vertices = [];

        // create a vertices
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            if (!isset($vertices[$layer])) {
                $vertices[$layer] = $graph->createVertex($layer);
            }

            foreach ($layersDependOn as $layerDependOn => $layerDependOnCount) {
                if (!isset($vertices[$layerDependOn])) {
                    $vertices[$layerDependOn] = $graph->createVertex($layerDependOn);
                }
            }
        }

        // createEdges
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            foreach ($layersDependOn as $layerDependOn => $layerDependOnCount) {
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

    /**
     * @param RulesetViolation[] $violations
     * @return array
     */
    private function calculateViolations(array $violations)
    {
        $layerViolations = [];
        foreach ($violations as $violation) {
            if (!isset($layerViolations[$violation->getLayerA()])) {
                $layerViolations[$violation->getLayerA()] = [];
            }

            if (!isset($layerViolations[$violation->getLayerA()][$violation->getLayerB()])) {
                $layerViolations[$violation->getLayerA()][$violation->getLayerB()] = 1;
            } else {
                $layerViolations[$violation->getLayerA()][$violation->getLayerB(
                )] = $layerViolations[$violation->getLayerA()][$violation->getLayerB()] + 1;
            }
        }

        return $layerViolations;
    }

    /**
     * @param AstMap $astMap
     * @param DependencyResult $dependencyResult
     * @param ClassNameLayerResolverInterface $classNameLayerResolver
     * @return array
     */
    private function calculateLayerDependencies(
        AstMap $astMap,
        DependencyResult $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver
    ) {
        $layersDependOnLayers = [];

        // all classes
        foreach ($astMap->getAstClassReferences() as $classReferences) {
            foreach ($classNameLayerResolver->getLayersByClassName(
                $classReferences->getClassName()
            ) as $classReferenceLayer) {
                $layersDependOnLayers[$classReferenceLayer] = [];
            }
        }

        // dependencies
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

                foreach ($layersB as $layerB) {
                    if ($layerA == $layerB) {
                        continue;
                    }

                    if (!isset($layersDependOnLayers[$layerA][$layerB])) {
                        $layersDependOnLayers[$layerA][$layerB] = 1;
                        continue;
                    }

                    $layersDependOnLayers[$layerA][$layerB] = $layersDependOnLayers[$layerA][$layerB] + 1;
                }

            }
        }

        return $layersDependOnLayers;
    }
}
