<?php

namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Contract\Result\CoveredRuleInterface;
use Qossmic\Deptrac\Contract\Result\LegacyResult;
use Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\FormatterConfiguration;
use Symfony\Component\Console\Output\BufferedOutput;

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
        $violations = $result->violations();

        if (null !== $outputFormatterInput->outputPath) {
            $output = new BufferedOutput();
        }

        $output->writeln('flowchart '.$this->config['direction'].';');

        if ([] !== $this->config['groups']) {
            foreach ($this->config['groups'] as $subGraphName => $layers) {
                $output->writeln('  subgraph '.$subGraphName.'Group;');

                foreach ($layers as $layer) {
                    $output->writeln('    '.$layer.';');
                }

                $output->writeln('  end;');
            }
        }

        $linkCount = 0;
        $violationsLinks = [];
        $violationGraphLinks = [];

        if ([] !== $violations) {
            foreach ($violations as $violation) {
                if (!isset($violationsLinks[$violation->getDependerLayer()][$violation->getDependentLayer()])) {
                    $violationsLinks[$violation->getDependerLayer()][$violation->getDependentLayer()] = 1;
                } else {
                    ++$violationsLinks[$violation->getDependerLayer()][$violation->getDependentLayer()];
                }
            }

            foreach ($violationsLinks as $dependerLayer => $layers) {
                foreach ($layers as $dependentLayer => $count) {
                    $output->writeln('    '.$dependerLayer.' -->|'.$count.'| '.$dependentLayer.';');
                    $violationGraphLinks[] = $linkCount;
                    ++$linkCount;
                }
            }
        }

        if ([] !== $graph) {
            foreach ($graph as $dependerLayer => $layers) {
                foreach ($layers as $dependentLayer => $count) {
                    if (!isset($violationsLinks[$dependerLayer][$dependentLayer])) {
                        $output->writeln('    '.$dependerLayer.' -->|'.(string) $count.'| '.$dependentLayer.';');
                    }
                }
            }
        }

        if ([] !== $violationGraphLinks) {
            foreach ($violationGraphLinks as $linkNumber) {
                $output->writeln('    linkStyle '.$linkNumber.' stroke:red,stroke-width:4px;');
            }
        }

        $path = $outputFormatterInput->outputPath;
        if ($path) {
            /** @var BufferedOutput $output */
            $content = $output->fetch();
            if ($content) {
                file_put_contents($path, $content);
            }
        }
    }

    /**
     * @return array<string, array<string, int<1, max>>>
     */
    protected function parseResults(LegacyResult $result): array
    {
        $graph = [];

        foreach ($result->allowed() as $rule) {
            if ($rule instanceof CoveredRuleInterface) {
                if (!isset($graph[$rule->getDependerLayer()][$rule->getDependentLayer()])) {
                    $graph[$rule->getDependerLayer()][$rule->getDependentLayer()] = 1;
                } else {
                    ++$graph[$rule->getDependerLayer()][$rule->getDependentLayer()];
                }
            }
        }

        return $graph;
    }
}
