<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use LogicException;
use phpDocumentor\GraphViz\Graph;
use Qossmic\Deptrac\Configuration\OutputFormatterInput;
use Qossmic\Deptrac\Console\Output;

/**
 * @internal
 */
final class GraphVizOutputDotFormatter extends GraphVizOutputFormatter
{
    public static function getName(): string
    {
        return 'graphviz-dot';
    }

    protected function output(Graph $graph, Output $output, OutputFormatterInput $outputFormatterInput): void
    {
        $dumpDotPath = $outputFormatterInput->getOutputPath();
        if (null === $dumpDotPath) {
            throw new LogicException("No '--output' defined for GraphViz formatter");
        }

        file_put_contents($dumpDotPath, (string) $graph);
        $output->writeLineFormatted('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
    }
}
