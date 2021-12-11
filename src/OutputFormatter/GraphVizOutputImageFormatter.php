<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use phpDocumentor\GraphViz\Exception;
use phpDocumentor\GraphViz\Graph;
use Qossmic\Deptrac\Console\Output;

final class GraphVizOutputImageFormatter extends GraphVizOutputFormatter
{
    public static function getName(): string
    {
        return 'graphviz-image';
    }

    protected function output(Graph $graph, Output $output, OutputFormatterInput $outputFormatterInput): void
    {
        $dumpImagePath = $outputFormatterInput->getOutputPath();
        if (null !== $dumpImagePath) {
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
    }
}
