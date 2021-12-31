<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use SplFileInfo;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Iterator\PathFilterIterator;
use const DIRECTORY_SEPARATOR;

class PathNameFilterIterator extends PathFilterIterator
{
    public function accept(): bool
    {
        /** @var SplFileInfo $fileInfo */
        $fileInfo = $this->current();
        $filename = $this->isWindows() ? Path::normalize($fileInfo->getPathname()) : $fileInfo->getPathName();

        return $this->isAccepted($filename);
    }

    private function isWindows(): bool
    {
        return '\\' === DIRECTORY_SEPARATOR;
    }
}
