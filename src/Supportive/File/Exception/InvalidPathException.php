<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\File\Exception;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;
use SplFileInfo;
use DEPTRAC_202401\Symfony\Component\Filesystem\Path;
use function sprintf;
final class InvalidPathException extends RuntimeException implements ExceptionInterface
{
    public static function unreadablePath(SplFileInfo $path) : self
    {
        return new self(sprintf('Path "%s" is not a directory or is not readable.', Path::canonicalize($path->getPathname())));
    }
}
