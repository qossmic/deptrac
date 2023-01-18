<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use phpDocumentor\GraphViz\Exception;
use phpDocumentor\GraphViz\Graph;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputException;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;

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

    protected function output(Graph $graph, OutputInterface $output, OutputFormatterInput $outputFormatterInput): void
    {
        $dumpHtmlPath = $outputFormatterInput->outputPath;
        if (null === $dumpHtmlPath) {
            throw OutputException::withMessage("No '--output' defined for GraphViz formatter");
        }

        try {
            $filename = $this->getTempImage($graph);
            $imageData = file_get_contents($filename);
            if (false === $imageData) {
                throw OutputException::withMessage('Unable to create temp file for output.');
            }
            file_put_contents(
                $dumpHtmlPath,
                '<img src="data:image/png;base64,'.base64_encode($imageData).'" />'
            );
            $output->writeLineFormatted('<info>HTML dumped to '.realpath($dumpHtmlPath).'</info>');
        } catch (Exception $exception) {
            throw OutputException::withMessage('Unable to generate HTML file: '.$exception->getMessage());
        } finally {
            /** @psalm-suppress RedundantCondition */
            if (isset($filename) && false !== $filename) {
                unlink($filename);
            }
        }
    }
}
