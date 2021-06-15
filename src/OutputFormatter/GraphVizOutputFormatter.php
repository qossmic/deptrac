<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Graphp\GraphViz\GraphViz;
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

        $graph = new Graph();

        /** @var Vertex[] $vertices */
        $vertices = [];

        $outputConfig = ConfigurationGraphViz::fromArray($outputFormatterInput->getConfig());
        $hiddenLayers = $outputConfig->getHiddenLayers();
        // create a vertices
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            if (in_array($layer, $hiddenLayers, true)) {
                continue;
            }
            if (!isset($vertices[$layer])) {
                $vertices[$layer] = $graph->createVertex($layer);
            }

            foreach ($layersDependOn as $layerDependOn => $layerDependOnCount) {
                if (in_array($layerDependOn, $hiddenLayers, true)) {
                    continue;
                }
                if (!isset($vertices[$layerDependOn])) {
                    $vertices[$layerDependOn] = $graph->createVertex($layerDependOn);
                }
            }
        }

        $groupNumber = 1;
        foreach ($outputConfig->getGroupsLayerMap() as $groupName => $groupLayerNames) {
            foreach ($groupLayerNames as $groupLayerName) {
                if (array_key_exists($groupLayerName, $vertices)) {
                    //TODO: Remove next line once graphviz library is updated to 1.0
                    $vertices[$groupLayerName]->setGroup($groupNumber);

                    $vertices[$groupLayerName]->setAttribute('group', $groupName);
                    $vertices[$groupLayerName]->setAttribute('graphviz.group', $groupName);
                }
            }
            ++$groupNumber;
        }

        // createEdges
        foreach ($layersDependOnLayers as $layer => $layersDependOn) {
            if (in_array($layer, $hiddenLayers, true)) {
                continue;
            }
            foreach ($layersDependOn as $layerDependOn => $layerDependOnCount) {
                if (in_array($layerDependOn, $hiddenLayers, true)) {
                    continue;
                }
                $edge = $vertices[$layer]->createEdgeTo($vertices[$layerDependOn]);

                if (isset($layerViolations[$layer][$layerDependOn])) {
                    $edge->setAttribute('graphviz.label', $layerViolations[$layer][$layerDependOn]);
                    $edge->setAttribute('graphviz.color', 'red');
                } else {
                    $edge->setAttribute('graphviz.label', $layerDependOnCount);
                }
            }
        }

        if ($outputFormatterInput->getOptionAsBoolean(self::DISPLAY)) {
            (new GraphViz())->display($graph);
        }

        if ($dumpImagePath = $outputFormatterInput->getOption(self::DUMP_IMAGE)) {
            $imagePath = (new GraphViz())->createImageFile($graph);
            rename($imagePath, $dumpImagePath);
            $output->writeLineFormatted('<info>Image dumped to '.realpath($dumpImagePath).'</info>');
        }

        if ($dumpDotPath = $outputFormatterInput->getOption(self::DUMP_DOT)) {
            file_put_contents($dumpDotPath, (new GraphViz())->createScript($graph));
            $output->writeLineFormatted('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
        }

        if ($dumpHtmlPath = $outputFormatterInput->getOption(self::DUMP_HTML)) {
            file_put_contents($dumpHtmlPath, (new GraphViz())->createImageHtml($graph));
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
}
