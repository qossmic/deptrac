<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use const DIRECTORY_SEPARATOR;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Iterator\PathFilterIterator;

class PathNameFilterIterator extends PathFilterIterator
{
    public function accept(): bool
    {
        $fileInfo = $this->current();
        $filename = $this->isWindows() ? Path::normalize($fileInfo->getPathname()) : $fileInfo->getPathName();

        return $this->isAccepted($filename);
    }

    private function isWindows(): bool
    {
        return '\\' === DIRECTORY_SEPARATOR;
    }
}
