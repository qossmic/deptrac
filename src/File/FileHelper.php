<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\File;

use function preg_match;
use function strlen;
use function strpos;
use function substr;
use function trim;

final class FileHelper
{
    /** @var string */
    private $workingDirectory;

    public function __construct(string $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function toAbsolutePath(string $path): string
    {
        $path = trim($path);

        if (0 === strpos($path, '/')) {
            return $path;
        }

        if ('\\' === $path[0] || (strlen($path) >= 3 && preg_match('#^[A-Z]:[/\\\]#i', substr($path, 0, 3)))) {
            return $path;
        }

        if (false !== strpos($path, '://')) {
            return $path;
        }

        if (0 === strpos($path, './')) {
            $path = substr($path, 2);
        }

        return $this->workingDirectory.'/'.$path;
    }
}
