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
    private array $config;

    /**
     * @param FormatterConfiguration $config
     */
    public function __construct(FormatterConfiguration $config)
    {
        /** @var array{hidden_layers?: string[], groups?: array<string, string[]>, pointToGroups?: bool}  $extractedConfig */
        $extractedConfig = $config->getConfigFor('mermaidjs');
        $this->config = $extractedConfig;
    }

    public static function getName(): string
    {
        return 'mermaidjs';
    }

    /**
     * @param LegacyResult $result
     * @param OutputInterface $output
     * @param OutputFormatterInput $outputFormatterInput
     * @return void
     */
    public function finish(LegacyResult $result, OutputInterface $output, OutputFormatterInput $outputFormatterInput): void
    {
        $graph = $this->parseResults($result);

        $output->writeLineFormatted('flowchart TD;');

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
                foreach ($layers as $depentendLayer => $count) {
                    $output->writeLineFormatted('    '.$dependerLayer.' -->|'.(string) $count.'| '.$depentendLayer.';');
                }
            }
        }
    }

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
