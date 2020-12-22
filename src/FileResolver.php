<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use Iterator;
use SensioLabs\Deptrac\Configuration\Configuration;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class FileResolver
{
    /**
     * @return SplFileInfo[]
     */
    public function resolve(Configuration $configuration): array
    {
        $finder = (new Finder())
            ->in($configuration->getPaths())
            ->name('*.php')
            ->files()
            ->followLinks()
            ->ignoreUnreadableDirs(true)
            ->ignoreVCS(true)
            ->notPath($configuration->getExcludeFiles());

        $customFilterIterator = $finder->getIterator();

        if (!$customFilterIterator instanceof Iterator) {
            throw new \RuntimeException('unable to create an iterator for the configured paths');
        }

        $finder = new PathNameFilterIterator($customFilterIterator, [], $configuration->getExcludeFiles());

        return iterator_to_array($finder);
    }
}
