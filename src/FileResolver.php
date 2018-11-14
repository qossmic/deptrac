<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\Configuration\Configuration;
use Symfony\Component\Finder\Finder;

class FileResolver
{
    /**
     * @throws \InvalidArgumentException
     *
     * @return \SplFileInfo[]
     */
    public function resolve(Configuration $configuration): array
    {
        $files = iterator_to_array(
            (new Finder())
                ->in($configuration->getPaths())
                ->name('*.php')
                ->files()
                ->followLinks()
                ->ignoreUnreadableDirs(true)
                ->ignoreVCS(true)
        );

        return array_filter($files, function (\SplFileInfo $fileInfo) use ($configuration) {
            foreach ($configuration->getExcludeFiles() as $excludeFiles) {
                if (preg_match('/'.$excludeFiles.'/i', $fileInfo->getPathname())) {
                    return false;
                }
            }

            return true;
        });
    }
}
