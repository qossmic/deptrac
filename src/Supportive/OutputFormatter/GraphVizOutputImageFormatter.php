<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use DEPTRAC_202401\phpDocumentor\GraphViz\Exception;
use DEPTRAC_202401\phpDocumentor\GraphViz\Graph;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputException;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use SplFileInfo;
use DEPTRAC_202401\Symfony\Component\Filesystem\Path;
/**
 * @internal
 */
final class GraphVizOutputImageFormatter extends \Qossmic\Deptrac\Supportive\OutputFormatter\GraphVizOutputFormatter
{
    public static function getName() : string
    {
        return 'graphviz-image';
    }
    protected function output(Graph $graph, OutputInterface $output, OutputFormatterInput $outputFormatterInput) : void
    {
        $dumpImagePath = $outputFormatterInput->outputPath;
        if (null === $dumpImagePath) {
            throw OutputException::withMessage("No '--output' defined for GraphViz formatter");
        }
        $imageFile = new SplFileInfo($dumpImagePath);
        $imagePathInfo = $imageFile->getPathInfo();
        /** @phpstan-ignore-next-line false positive */
        if (null === $imagePathInfo) {
            throw OutputException::withMessage('Unable to dump image: Invalid or missing path.');
        }
        if (!$imagePathInfo->isWritable()) {
            throw OutputException::withMessage(\sprintf('Unable to dump image: Path "%s" does not exist or is not writable.', Path::canonicalize($imagePathInfo->getPathname())));
        }
        try {
            $graph->export($imageFile->getExtension() ?: 'png', $imageFile->getPathname());
            $output->writeLineFormatted('<info>Image dumped to ' . $imageFile->getPathname() . '</info>');
        } catch (Exception $exception) {
            throw OutputException::withMessage('Unable to display output: ' . $exception->getMessage());
        }
    }
}
