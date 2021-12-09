<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\File\FileHelper;
use Symfony\Component\Finder\Iterator\PathFilterIterator;

class PathNameFilterIterator extends PathFilterIterator
{
    public function accept(): bool
    {
        $fileInfo = $this->current();
        $filename = $fileInfo->getPathname();

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $filename = FileHelper::normalizePath($filename);
        }

        return $this->isAccepted($filename);
    }
}
