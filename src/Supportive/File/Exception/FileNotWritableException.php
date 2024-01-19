<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\File\Exception;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;
use SplFileInfo;
use DEPTRAC_202401\Symfony\Component\Filesystem\Path;
use function sprintf;
class FileNotWritableException extends RuntimeException implements ExceptionInterface
{
    public static function notWritable(SplFileInfo $file) : self
    {
        return new self(sprintf('Could not write file "%s".', Path::canonicalize($file->getPathname())));
    }
}
