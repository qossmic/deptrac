<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Graphp\GraphViz\GraphViz;
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
    private const LEGACY_OPTION_PREFIX = 'formatter-';
    public const DISPLAY = self::NAME.'-display';
    public const DUMP_IMAGE = self::NAME.'-dump-image';
    public const DUMP_DOT = self::NAME.'-dump-dot';
    public const DUMP_HTML = self::NAME.'-dump-html';

    /** @deprecated  */
    public const LEGACY_DISPLAY = self::LEGACY_OPTION_PREFIX.self::DISPLAY;
    /** @deprecated  */
    public const LEGACY_DUMP_IMAGE = self::LEGACY_OPTION_PREFIX.self::DUMP_IMAGE;
    /** @deprecated  */
    public const LEGACY_DUMP_DOT = self::LEGACY_OPTION_PREFIX.self::DUMP_DOT;
    /** @deprecated  */
    public const LEGACY_DUMP_HTML = self::LEGACY_OPTION_PREFIX.self::DUMP_HTML;

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
            OutputFormatterOption::newValueOption(self::LEGACY_DISPLAY, '<fg=yellow>[DEPRECATED]</> Should try to open graphviz image.'),
            OutputFormatterOption::newValueOption(self::LEGACY_DUMP_IMAGE, '<fg=yellow>[DEPRECATED]</> Path to a dumped png file.'),
            OutputFormatterOption::newValueOption(self::LEGACY_DUMP_DOT, '<fg=yellow>[DEPRECATED]</> Path to a dumped dot file.'),
            OutputFormatterOption::newValueOption(self::LEGACY_DUMP_HTML, '<fg=yellow>[DEPRECATED]</> Path to a dumped html file.'),
        ];
    }

    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $legacyDisplay = $outputFormatterInput->getOptionAsBoolean(self::LEGACY_DISPLAY);
        $legacyDumpImage = $outputFormatterInput->getOption(self::LEGACY_DUMP_IMAGE);
        $legacyDumpDot = $outputFormatterInput->getOption(self::LEGACY_DUMP_DOT);
        $legacyDumpHtml = $outputFormatterInput->getOption(self::LEGACY_DUMP_HTML);

        $this->reportDeprecation($legacyDisplay, self::LEGACY_DISPLAY, self::DISPLAY, $output);
        $this->reportDeprecation(!empty($legacyDumpImage), self::LEGACY_DUMP_IMAGE, self::DUMP_IMAGE, $output);
        $this->reportDeprecation(!empty($legacyDumpDot), self::LEGACY_DUMP_DOT, self::DUMP_DOT, $output);
        $this->reportDeprecation(!empty($legacyDumpHtml), self::LEGACY_DUMP_HTML, self::DUMP_HTML, $output);

        $layerViolations = $this->calculateViolations($context->violations());
        $layersDependOnLayers = $this->calculateLayerDependencies($context->rules());

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
                $edge = $vertices[$layer]->createEdgeTo($vertices[$layerDependOn]);

                if (isset($layerViolations[$layer][$layerDependOn])) {
                    $edge->setAttribute('graphviz.label', $layerViolations[$layer][$layerDependOn]);
                    $edge->setAttribute('graphviz.color', 'red');
                } else {
                    $edge->setAttribute('graphviz.label', $layerDependOnCount);
                }
            }
        }

        if ($legacyDisplay || $outputFormatterInput->getOptionAsBoolean(self::DISPLAY)) {
            (new GraphViz())->display($graph);
        }

        if (($dumpImagePath = $legacyDumpImage)
            || ($dumpImagePath = $outputFormatterInput->getOption(self::DUMP_IMAGE))
        ) {
            $imagePath = (new GraphViz())->createImageFile($graph);
            rename($imagePath, $dumpImagePath);
            $output->writeLineFormatted('<info>Image dumped to '.realpath($dumpImagePath).'</info>');
        }

        if (($dumpDotPath = $legacyDumpDot)
            || ($dumpDotPath = $outputFormatterInput->getOption(self::DUMP_DOT))
        ) {
            file_put_contents($dumpDotPath, (new GraphViz())->createScript($graph));
            $output->writeLineFormatted('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
        }

        if (($dumpHtmlPath = $legacyDumpHtml)
            || ($dumpHtmlPath = $outputFormatterInput->getOption(self::DUMP_HTML))
        ) {
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

    private function reportDeprecation(bool $report, string $legacyOption, string $newOption, Output $output): void
    {
        if ($report) {
            $output->writeLineFormatted(sprintf('⚠️  You\'re using an obsolete option <fg=cyan>--%s</>. ⚠️️', $legacyOption));
            $output->writeLineFormatted(sprintf('   Please use the new option <fg=cyan>--%s</> instead.', $newOption));
            $output->writeLineFormatted('');
        }
    }
}
