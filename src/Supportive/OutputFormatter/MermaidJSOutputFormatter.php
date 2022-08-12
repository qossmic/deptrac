<?php

namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Contract\Result\CoveredRuleInterface;
use Qossmic\Deptrac\Contract\Result\LegacyResult;
use Qossmic\Deptrac\Contract\Result\Uncovered;
use Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\FormatterConfiguration;

/**
 * @internal
 */
class MermaidJSOutputFormatter implements OutputFormatterInterface
{
    /** @var array{direction: string, groups: array<string, string[]>} */
    private array $config;

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

    public function finish(LegacyResult $result, OutputInterface $output, OutputFormatterInput $outputFormatterInput): void
    {
        $graph = $this->parseResults($result);

        $output->writeLineFormatted('flowchart '.$this->config['direction'].';');

        if ([] !== $this->config['groups']) {
            foreach ($this->config['groups'] as $subGraphName => $layers) {
                $output->writeLineFormatted('  subgraph '.$subGraphName.'Group;');

                foreach ($layers as $layer) {
                    $output->writeLineFormatted('    '.$layer.';');
                }

                $output->writeLineFormatted('  end;');
            }
        }

        if ([] !== $graph) {
            foreach ($graph as $dependerLayer => $layers) {
                foreach ($layers as $dependentLayer => $count) {
                    $output->writeLineFormatted('    '.$dependerLayer.' -->|'.(string) $count.'| '.$dependentLayer.';');
                }
            }
        }
    }

    /**
     * @return array<string, array<string, int<1, max>>>
     */
    protected function parseResults(LegacyResult $result): array
    {
        $graph = [];

        foreach ($result->rules() as $rule) {
            if ($rule instanceof CoveredRuleInterface) {
                if (!isset($graph[$rule->getDependerLayer()][$rule->getDependentLayer()])) {
                    $graph[$rule->getDependerLayer()][$rule->getDependentLayer()] = 1;
                } else {
                    ++$graph[$rule->getDependerLayer()][$rule->getDependentLayer()];
                }
            }

            if ($rule instanceof Uncovered) {
                continue;
            }
        }

        return $graph;
    }
}
