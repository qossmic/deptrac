<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use Fhaculty\Graph\Vertex;
use Graphp\GraphViz\GraphViz;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Symfony\Component\Console\Output\OutputInterface;

class GraphVizOutputFormatter implements OutputFormatterInterface
{
    protected $eventDispatcher;

    private static $argument_display = 'display';
    private static $argument_dump_image = 'dump-image';
    private static $argument_dump_dot = 'dump-dot';
    private static $argument_dump_html = 'dump-html';

    public function getName(): string
    {
        return 'graphviz';
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions(): array
    {
        return [
            OutputFormatterOption::newValueOption(static::$argument_display, 'should try to open graphviz image', true),
            OutputFormatterOption::newValueOption(static::$argument_dump_image, 'path to a dumped png file', ''),
            OutputFormatterOption::newValueOption(static::$argument_dump_dot, 'path to a dumped dot file', ''),
            OutputFormatterOption::newValueOption(static::$argument_dump_html, 'path to a dumped html file', ''),
        ];
    }

    public function finish(
        DependencyContext $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $layerViolations = $this->calculateViolations($dependencyContext->getViolations());

        $layersDependOnLayers = $this->calculateLayerDependencies(
            $dependencyContext->getAstMap(),
            $dependencyContext->getDependencyResult(),
            $dependencyContext->getClassNameLayerResolver()
        );

        $graph = new \Fhaculty\Graph\Graph();

        /** @var Vertex[] $vertices */
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

                if (isset($layerViolations[$layer][$layerDependOn])) {
                    $edge = $vertices[$layer]->getEdgesTo($vertices[$layerDependOn])->getEdgeFirst();
                    $edge->setAttribute('graphviz.label', $layerViolations[$layer][$layerDependOn]);
                    $edge->setAttribute('graphviz.color', 'red');
                }
            }
        }

        $display = $outputFormatterInput->getOption(static::$argument_display);
        if (true === filter_var($display, FILTER_VALIDATE_BOOLEAN)) {
            (new GraphViz())->display($graph);
        }

        if ($dumpImagePath = $outputFormatterInput->getOption(static::$argument_dump_image)) {
            $imagePath = (new GraphViz())->createImageFile($graph);
            rename($imagePath, $dumpImagePath);
            $output->writeln('<info>Image dumped to '.realpath($dumpImagePath).'</info>');
        }

        if ($dumpDotPath = $outputFormatterInput->getOption(static::$argument_dump_dot)) {
            file_put_contents($dumpDotPath, (new GraphViz())->createScript($graph));
            $output->writeln('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
        }

        if ($dumpHtmlPath = $outputFormatterInput->getOption(static::$argument_dump_html)) {
            file_put_contents($dumpHtmlPath, (new GraphViz())->createImageHtml($graph));
            $output->writeln('<info>HTML dumped to '.realpath($dumpHtmlPath).'</info>');
        }
    }

    /**
     * @param RulesetViolation[] $violations
     */
    private function calculateViolations(array $violations): array
    {
        $layerViolations = [];
        foreach ($violations as $violation) {
            if (!isset($layerViolations[$violation->getLayerA()])) {
                $layerViolations[$violation->getLayerA()] = [];
            }

            if (!isset($layerViolations[$violation->getLayerA()][$violation->getLayerB()])) {
                $layerViolations[$violation->getLayerA()][$violation->getLayerB()] = 1;
            } else {
                ++$layerViolations[$violation->getLayerA()][$violation->getLayerB()];
            }
        }

        return $layerViolations;
    }

    private function calculateLayerDependencies(
        AstMap $astMap,
        Result $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver
    ): array {
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
                    if ($layerA === $layerB) {
                        continue;
                    }

                    if (!isset($layersDependOnLayers[$layerA][$layerB])) {
                        $layersDependOnLayers[$layerA][$layerB] = 1;
                        continue;
                    }

                    ++$layersDependOnLayers[$layerA][$layerB];
                }
            }
        }

        return $layersDependOnLayers;
    }
}
