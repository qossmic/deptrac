<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Symfony\Component\Console\Output\OutputInterface;

final class DOTOutputFormatter implements OutputFormatterInterface
{
    private static $argument_dump_dot = 'dump-dot';

    public function getName(): string
    {
        return 'dot';
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
            OutputFormatterOption::newValueOption(static::$argument_dump_dot, 'path to a dumped dot file', ''),
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

        $graph = Graph::create();

        /** @var Node[] $nodes */
        $nodes = [];

        // create a vertices
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            if (!isset($nodes[$layer])) {
                $graph->setNode($nodes[$layer] = Node::create($layer));
            }

            foreach ($layersDependOn as $layerDependOn => $layerDependOnCount) {
                if (!isset($nodes[$layerDependOn])) {
                    $graph->setNode($nodes[$layerDependOn] = Node::create($layerDependOn));
                }
            }
        }

        // createEdges
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            foreach ($layersDependOn as $layerDependOn => $layerDependOnCount) {
                $edge = Edge::create($nodes[$layer], $nodes[$layerDependOn]);

                if (isset($layerViolations[$layer][$layerDependOn])) {
                    $edge->setAttribute('label', $layerViolations[$layer][$layerDependOn]);
                    $edge->setAttribute('color', 'red');
                }

                $graph->link($edge);
            }
        }

        if ($dumpDotPath = $outputFormatterInput->getOption(static::$argument_dump_dot)) {
            file_put_contents($dumpDotPath, (string) $graph);
            $output->writeln('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
        } else {
            $output->write((string) $graph);
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
