<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SplFileInfo;
use Symfony\Component\Finder\Iterator\PathFilterIterator;

/**
 * @method SplFileInfo current()
 */
class PathNameFilterIterator extends PathFilterIterator
{
    public function accept(): bool
    {
        $filename = $this->current()->getPathname();

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $filename = str_replace('\\', '/', $filename);
        }

        return $this->isAccepted($filename);
    }
}
