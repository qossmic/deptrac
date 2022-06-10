<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use LogicException;
use phpDocumentor\GraphViz\Exception;
use phpDocumentor\GraphViz\Graph;
use Qossmic\Deptrac\Configuration\OutputFormatterInput;
use Qossmic\Deptrac\Console\Output;
use SplFileInfo;
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 */
final class GraphVizOutputImageFormatter extends GraphVizOutputFormatter
{
    public static function getName(): string
    {
        return 'graphviz-image';
    }

    protected function output(Graph $graph, Output $output, OutputFormatterInput $outputFormatterInput): void
    {
        $dumpImagePath = $outputFormatterInput->getOutputPath();
        if (null === $dumpImagePath) {
            throw new LogicException("No '--output' defined for GraphViz formatter");
        }

        $imageFile = new SplFileInfo($dumpImagePath);
        if (!$imageFile->getPathInfo()->isWritable()) {
            throw new LogicException(sprintf('Unable to dump image: Path "%s" does not exist or is not writable.', Path::canonicalize($imageFile->getPathInfo()->getPathname())));
        }
        try {
            $graph->export($imageFile->getExtension() ?: 'png', $imageFile->getPathname());
            $output->writeLineFormatted('<info>Image dumped to '.$imageFile->getPathname().'</info>');
        } catch (Exception $exception) {
            throw new LogicException('Unable to display output: '.$exception->getMessage());
        }
    }
}
