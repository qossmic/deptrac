<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use function base64_encode;
use function file_get_contents;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Exception;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use Qossmic\Deptrac\Configuration\ConfigurationGraphViz;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\CoveredRule;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;
use function sys_get_temp_dir;
use function tempnam;

final class GraphVizOutputFormatter implements OutputFormatterInterface
{
    private const NAME = 'graphviz';
    public const DISPLAY = self::NAME.'-display';
    public const DUMP_IMAGE = self::NAME.'-dump-image';
    public const DUMP_DOT = self::NAME.'-dump-dot';
    public const DUMP_HTML = self::NAME.'-dump-html';
    /** @var positive-int */
    private const DELAY_OPEN = 2;

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

        /** @var array{hidden_layers?: string[], groups?: array<string, string[]>} $outputConfig */
        $outputConfig = $outputFormatterInput->getConfig();
        $outputConfig = ConfigurationGraphViz::fromArray($outputConfig);

        $graph = Graph::create('');
        $nodes = $this->createNodes($outputConfig, $layersDependOnLayers);
        $this->connectEdges($graph, $nodes, $outputConfig, $layersDependOnLayers, $layerViolations);
        $this->addNodesToGraph($graph, $nodes, $outputConfig);

        if ($outputFormatterInput->getOptionAsBoolean(self::DISPLAY)) {
            $this->display($graph);
        }

        if ($dumpImagePath = (string) $outputFormatterInput->getOption(self::DUMP_IMAGE)) {
            $imageFile = new \SplFileInfo($dumpImagePath);
            if (!is_dir($imageFile->getPath()) && !mkdir($imageFile->getPath())) {
                throw new \LogicException(sprintf('Unable to dump image: Path "%s" does not exist and is not writable.', $imageFile->getPath()));
            }
            try {
                $graph->export('png', $dumpImagePath);
                $output->writeLineFormatted('<info>Image dumped to '.realpath($dumpImagePath).'</info>');
            } catch (Exception $exception) {
                throw new \LogicException('Unable to display output: '.$exception->getMessage());
            }
        }

        if ($dumpDotPath = (string) $outputFormatterInput->getOption(self::DUMP_DOT)) {
            file_put_contents($dumpDotPath, (string) $graph);
            $output->writeLineFormatted('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
        }

        if ($dumpHtmlPath = (string) $outputFormatterInput->getOption(self::DUMP_HTML)) {
            try {
                $filename = $this->getTempImage($graph);
                $imageData = file_get_contents($filename);
                if (false === $imageData) {
                    throw new \RuntimeException('Unable to create temp file for output.');
                }
                file_put_contents(
                    $dumpHtmlPath,
                    '<img src="data:image/png;base64,'.base64_encode($imageData).'" />'
                );
                $output->writeLineFormatted('<info>HTML dumped to '.realpath($dumpHtmlPath).'</info>');
            } catch (Exception $exception) {
                throw new \LogicException('Unable to generate HTML file: '.$exception->getMessage());
            } finally {
                /** @psalm-suppress RedundantCondition */
                if (isset($filename) && false !== $filename) {
                    unlink($filename);
                }
            }
        }
    }

    public function display(Graph $graph): void
    {
        try {
            $filename = $this->getTempImage($graph);
            static $next = 0;
            if ($next > microtime(true)) {
                sleep(self::DELAY_OPEN);
            }

            if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
                exec('start "" '.escapeshellarg($filename).' >NUL');
            } elseif ('DARWIN' === strtoupper(PHP_OS)) {
                exec('open '.escapeshellarg($filename).' > /dev/null 2>&1 &');
            } else {
                exec('xdg-open '.escapeshellarg($filename).' > /dev/null 2>&1 &');
            }
            $next = microtime(true) + (float) self::DELAY_OPEN;
        } catch (Exception $exception) {
            throw new \LogicException('Unable to display output: '.$exception->getMessage());
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
            if (!isset($layerViolations[$violation->getDependantLayerName()])) {
                $layerViolations[$violation->getDependantLayerName()] = [];
            }

            if (!isset($layerViolations[$violation->getDependantLayerName()][$violation->getDependeeLayerName()])) {
                $layerViolations[$violation->getDependantLayerName()][$violation->getDependeeLayerName()] = 1;
            } else {
                ++$layerViolations[$violation->getDependantLayerName()][$violation->getDependeeLayerName()];
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
            if ($rule instanceof CoveredRule) {
                $layerA = $rule->getDependantLayerName();
                $layerB = $rule->getDependeeLayerName();

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
     * @param array<string, array<string, int>> $layersDependOnLayers
     *
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

            foreach ($layersDependOn as $layerDependOn => $_) {
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

    /**
     * @param Node[]                            $nodes
     * @param array<string, array<string, int>> $layersDependOnLayers
     * @param array<string, array<string, int>> $layerViolations
     */
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
                    $edge->setLabel((string) $layerViolations[$layer][$layerDependOn]);
                    $edge->setColor('red');
                } else {
                    $edge->setLabel((string) $layerDependOnCount);
                }
            }
        }
    }

    /**
     * @param Node[] $nodes
     */
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

    /**
     * @throws Exception
     */
    private function getTempImage(Graph $graph): string
    {
        $filename = tempnam(sys_get_temp_dir(), 'deptrac');
        if (false === $filename) {
            throw new \RuntimeException('Unable to create temp file for output.');
        }
        $filename .= '.png';
        $graph->export('png', $filename);

        return $filename;
    }
}
