<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Graphp\GraphViz\GraphViz;
use SensioLabs\Deptrac\RulesetEngine\Allowed;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Uncovered;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\OutputInterface;

class GraphVizOutputFormatter implements OutputFormatterInterface
{
    private const ARGUMENT_DISPLAY = 'display';
    private const ARGUMENT_DUMP_IMAGE = 'dump-image';
    private const ARGUMENT_DUMP_DOT = 'dump-dot';
    private const ARGUMENT_DUMP_HTML = 'dump-html';

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
            OutputFormatterOption::newValueOption(static::ARGUMENT_DISPLAY, 'should try to open graphviz image', true),
            OutputFormatterOption::newValueOption(static::ARGUMENT_DUMP_IMAGE, 'path to a dumped png file', ''),
            OutputFormatterOption::newValueOption(static::ARGUMENT_DUMP_DOT, 'path to a dumped dot file', ''),
            OutputFormatterOption::newValueOption(static::ARGUMENT_DUMP_HTML, 'path to a dumped html file', ''),
        ];
    }

    public function finish(
        Context $context,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $layerViolations = $this->calculateViolations($context->violations());
        $layersDependOnLayers = $this->calculateLayerDependencies($context->all());

        $graph = new Graph();

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

        $display = $outputFormatterInput->getOption(static::ARGUMENT_DISPLAY);
        if (true === filter_var($display, FILTER_VALIDATE_BOOLEAN)) {
            (new GraphViz())->display($graph);
        }

        if ($dumpImagePath = $outputFormatterInput->getOption(static::ARGUMENT_DUMP_IMAGE)) {
            $imagePath = (new GraphViz())->createImageFile($graph);
            rename($imagePath, $dumpImagePath);
            $output->writeln('<info>Image dumped to '.realpath($dumpImagePath).'</info>');
        }

        if ($dumpDotPath = $outputFormatterInput->getOption(static::ARGUMENT_DUMP_DOT)) {
            file_put_contents($dumpDotPath, (new GraphViz())->createScript($graph));
            $output->writeln('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
        }

        if ($dumpHtmlPath = $outputFormatterInput->getOption(static::ARGUMENT_DUMP_HTML)) {
            file_put_contents($dumpHtmlPath, (new GraphViz())->createImageHtml($graph));
            $output->writeln('<info>HTML dumped to '.realpath($dumpHtmlPath).'</info>');
        }
    }

    /**
     * @param Violation[] $violations
     *
     * @return array<string, array<string, int>>
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

    /**
     * @param Rule[] $rules
     *
     * @return array<string, array<string, int>>
     */
    private function calculateLayerDependencies(array $rules): array
    {
        $layersDependOnLayers = [];

        foreach ($rules as $rule) {
            if ($rule instanceof Violation
                || $rule instanceof SkippedViolation
                || $rule instanceof Allowed
            ) {
                $layerA = $rule->getLayerA();
                $layerB = $rule->getLayerB();

                if (!isset($layersDependOnLayers[$layerA])) {
                    $layersDependOnLayers[$layerA] = [];
                }

                if (!isset($layersDependOnLayers[$layerA][$layerB])) {
                    $layersDependOnLayers[$layerA][$layerB] = 1;
                    continue;
                }

                ++$layersDependOnLayers[$layerA][$layerB];
            } elseif ($rule instanceof Uncovered) {
                $layer = $rule->getLayer();
                if (!isset($layersDependOnLayers[$layer])) {
                    $layersDependOnLayers[$layer] = [];
                }
            }
        }

        return $layersDependOnLayers;
    }
}
