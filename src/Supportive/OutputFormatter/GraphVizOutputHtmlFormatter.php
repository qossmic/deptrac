<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use LogicException;
use phpDocumentor\GraphViz\Exception;
use phpDocumentor\GraphViz\Graph;
use Qossmic\Deptrac\Contract\OutputFormatter\Output;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use RuntimeException;

use function base64_encode;
use function file_get_contents;

/**
 * @internal
 */
final class GraphVizOutputHtmlFormatter extends GraphVizOutputFormatter
{
    public static function getName(): string
    {
        return 'graphviz-html';
    }

    protected function output(Graph $graph, Output $output, OutputFormatterInput $outputFormatterInput): void
    {
        $dumpHtmlPath = $outputFormatterInput->getOutputPath();
        if (null === $dumpHtmlPath) {
            throw new LogicException("No '--output' defined for GraphViz formatter");
        }

        try {
            $filename = $this->getTempImage($graph);
            $imageData = file_get_contents($filename);
            if (false === $imageData) {
                throw new RuntimeException('Unable to create temp file for output.');
            }
            file_put_contents(
                $dumpHtmlPath,
                '<img src="data:image/png;base64,'.base64_encode($imageData).'" />'
            );
            $output->writeLineFormatted('<info>HTML dumped to '.realpath($dumpHtmlPath).'</info>');
        } catch (Exception $exception) {
            throw new LogicException('Unable to generate HTML file: '.$exception->getMessage());
        } finally {
            /** @psalm-suppress RedundantCondition */
            if (isset($filename) && false !== $filename) {
                unlink($filename);
            }
        }
    }
}
