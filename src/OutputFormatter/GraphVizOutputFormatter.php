<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use Qossmic\Deptrac\Configuration\ConfigurationGraphViz;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\RulesetEngine\Allowed;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;

final class GraphVizOutputFormatter implements OutputFormatterInterface
{
    private const NAME = 'graphviz';
    public const DISPLAY = self::NAME.'-display';
    public const DUMP_IMAGE = self::NAME.'-dump-image';
    public const DUMP_DOT = self::NAME.'-dump-dot';
    public const DUMP_HTML = self::NAME.'-dump-html';

    public function getName(): string
    {
        return self::NAME;
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
            OutputFormatterOption::newValueOption(self::DISPLAY, 'Should try to open graphviz image.', true),
            OutputFormatterOption::newValueOption(self::DUMP_IMAGE, 'Path to a dumped png file.'),
            OutputFormatterOption::newValueOption(self::DUMP_DOT, 'Path to a dumped dot file.'),
            OutputFormatterOption::newValueOption(self::DUMP_HTML, 'Path to a dumped html file.'),
        ];
    }

    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $layerViolations = $this->calculateViolations($context->violations());
        $layersDependOnLayers = $this->calculateLayerDependencies($context->rules());
        $outputConfig = ConfigurationGraphViz::fromArray($outputFormatterInput->getConfig());

        $graph = Graph::create('');
        $nodes = $this->createNodes($outputConfig, $layersDependOnLayers);
        $this->connectEdges($graph, $nodes, $outputConfig, $layersDependOnLayers, $layerViolations);
        $this->addNodesToGraph($graph, $nodes, $outputConfig);

        if ($outputFormatterInput->getOptionAsBoolean(self::DISPLAY)) {
            $graph->export('xlib', tempnam(sys_get_temp_dir(), 'deptrac'));
        }

        if ($dumpImagePath = $outputFormatterInput->getOption(self::DUMP_IMAGE)) {
//            $graph->export('png',$dumpImagePath);
            $output->writeLineFormatted('<info>Image dumped to '.realpath($dumpImagePath).'</info>');
        }

        if ($dumpDotPath = $outputFormatterInput->getOption(self::DUMP_DOT)) {
//            file_put_contents($dumpDotPath, (string)$graph);
            $output->writeLineFormatted('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
        }

        if ($dumpHtmlPath = $outputFormatterInput->getOption(self::DUMP_HTML)) {
//            file_put_contents($dumpHtmlPath, (new GraphViz())->createImageHtml($graph));
            $output->writeLineFormatted('<info>HTML dumped to '.realpath($dumpHtmlPath).'</info>');
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

    /**
     * @return Node[]
     */
    private function createNodes(ConfigurationGraphViz $outputConfig, array $layersDependOnLayers): array
    {
        $hiddenLayers = $outputConfig->getHiddenLayers();
        $nodes = [];
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            if (in_array($layer, $hiddenLayers, true)) {
                continue;
            }
            if (!isset($nodes[$layer])) {
                $nodes[$layer] = new Node($layer);
            }

            foreach ($layersDependOn as $layerDependOn => $layerDependOnCount) {
                if (in_array($layerDependOn, $hiddenLayers, true)) {
                    continue;
                }
                if (!isset($nodes[$layerDependOn])) {
                    $nodes[$layerDependOn] = new Node($layerDependOn);
                }
            }
        }
        return $nodes;
    }

    private function connectEdges(
        Graph $graph,
        array $nodes,
        ConfigurationGraphViz $outputConfig,
        array $layersDependOnLayers,
        array $layerViolations
    ): void {
        $hiddenLayers = $outputConfig->getHiddenLayers();

        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            if (in_array($layer, $hiddenLayers, true)) {
                continue;
            }
            foreach ($layersDependOn as $layerDependOn => $layerDependOnCount) {
                if (in_array($layerDependOn, $hiddenLayers, true)) {
                    continue;
                }
                $edge = new Edge($nodes[$layer], $nodes[$layerDependOn]);
                $graph->link($edge);
                if (isset($layerViolations[$layer][$layerDependOn])) {
                    $edge->setAttribute('label', (string)$layerViolations[$layer][$layerDependOn]);
                    $edge->setAttribute('color', 'red');
                } else {
                    $edge->setAttribute('label', (string)$layerDependOnCount);
                }
            }
        }
    }

    private function addNodesToGraph(Graph $graph, array $nodes, ConfigurationGraphViz $outputConfig): void
    {
        foreach ($outputConfig->getGroupsLayerMap() as $groupName => $groupLayerNames) {
            $subgraph = Graph::create('cluster_'.$groupName)
                ->setAttribute('label', $groupName);
            $graph->addGraph($subgraph);

            foreach ($groupLayerNames as $groupLayerName) {
                if (array_key_exists($groupLayerName, $nodes)) {
                    $subgraph->setNode($nodes[$groupLayerName]);
                    $nodes[$groupLayerName]->setAttribute('group', $groupName);
                    unset($nodes[$groupLayerName]);
                }
            }
        }

        foreach ($nodes as $node) {
            $graph->setNode($node);
        }
    }
}
