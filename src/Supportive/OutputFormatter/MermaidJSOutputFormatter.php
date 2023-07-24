<?php

namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\FormatterConfiguration;

/**
 * @internal
 */
final class MermaidJSOutputFormatter implements OutputFormatterInterface
{
    /** @var array{direction: string, groups: array<string, string[]>} */
    private array $config;

    private const GRAPH_NODE_FORMAT = '    %s -->|%d| %s;';
    private const VIOLATION_STYLE_FORMAT = '    linkStyle %d stroke:red,stroke-width:4px;';

    public function __construct(FormatterConfiguration $config)
    {
        /** @var array{direction: string, groups: array<string, string[]>}  $extractedConfig */
        $extractedConfig = $config->getConfigFor('mermaidjs');
        $this->config = $extractedConfig;
    }

    public static function getName(): string
    {
        return 'mermaidjs';
    }

    public function finish(
        OutputResult $result,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $graph = $this->parseResults($result);
        $violations = $result->violations();
        $buffer = '';

        $output->writeLineFormatted(sprintf('flowchart %s;', $this->config['direction']));
        $buffer .= sprintf('flowchart %s;'.PHP_EOL, $this->config['direction']);

        foreach ($this->config['groups'] as $subGraphName => $layers) {
            $output->writeLineFormatted(sprintf('  subgraph %sGroup;', $subGraphName));
            $buffer .= sprintf('  subgraph %sGroup;'.PHP_EOL, $subGraphName);

            foreach ($layers as $layer) {
                $output->writeLineFormatted(sprintf('    %s;', $layer));
                $buffer .= sprintf('    %s;'.PHP_EOL, $layer);
            }

            $output->writeLineFormatted('  end;');
            $buffer .= '  end;'.PHP_EOL;
        }

        $linkCount = 0;
        $violationsLinks = [];
        $violationGraphLinks = [];

        foreach ($violations as $violation) {
            if (!isset($violationsLinks[$violation->getDependerLayer()][$violation->getDependentLayer()])) {
                $violationsLinks[$violation->getDependerLayer()][$violation->getDependentLayer()] = 1;
            } else {
                ++$violationsLinks[$violation->getDependerLayer()][$violation->getDependentLayer()];
            }
        }

        foreach ($violationsLinks as $dependerLayer => $layers) {
            foreach ($layers as $dependentLayer => $count) {
                $output->writeLineFormatted(sprintf(self::GRAPH_NODE_FORMAT, $dependerLayer, $count, $dependentLayer));
                $buffer .= sprintf(self::GRAPH_NODE_FORMAT.PHP_EOL, $dependerLayer, $count, $dependentLayer);
                $violationGraphLinks[] = $linkCount;
                ++$linkCount;
            }
        }

        foreach ($graph as $dependerLayer => $layers) {
            foreach ($layers as $dependentLayer => $count) {
                if (!isset($violationsLinks[$dependerLayer][$dependentLayer])) {
                    $output->writeLineFormatted(sprintf(self::GRAPH_NODE_FORMAT, $dependerLayer, $count, $dependentLayer));
                    $buffer .= sprintf(self::GRAPH_NODE_FORMAT.PHP_EOL, $dependerLayer, $count, $dependentLayer);
                }
            }
        }

        foreach ($violationGraphLinks as $linkNumber) {
            $output->writeLineFormatted(sprintf(self::VIOLATION_STYLE_FORMAT, $linkNumber));
            $buffer .= sprintf(self::VIOLATION_STYLE_FORMAT.PHP_EOL, $linkNumber);
        }

        if ($outputFormatterInput->outputPath) {
            file_put_contents($outputFormatterInput->outputPath, $buffer);
        }
    }

    /**
     * @return array<string, array<string, int<1, max>>>
     */
    protected function parseResults(OutputResult $result): array
    {
        $graph = [];

        foreach ($result->allowed() as $rule) {
            if (!isset($graph[$rule->getDependerLayer()][$rule->getDependentLayer()])) {
                $graph[$rule->getDependerLayer()][$rule->getDependentLayer()] = 1;
            } else {
                ++$graph[$rule->getDependerLayer()][$rule->getDependentLayer()];
            }
        }

        return $graph;
    }
}
