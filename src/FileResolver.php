<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\Configuration\Configuration;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class FileResolver
{
    /**
     * @return string[]
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

        $finder = new PathNameFilterIterator($customFilterIterator, [], $configuration->getExcludeFiles());

        return array_map(
            static function (SplFileInfo $fileInfo) {
                return (string) $fileInfo->getRealPath();
            },
            iterator_to_array($finder)
        );
    }
}
