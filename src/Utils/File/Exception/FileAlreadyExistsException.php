<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Utils\File\Exception;

use Qossmic\Deptrac\Utils\ExceptionInterface;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Filesystem\Path;
use function sprintf;

final class FileAlreadyExistsException extends RuntimeException implements ExceptionInterface
{
    public static function alreadyExists(SplFileInfo $file): self
    {
        return new self(sprintf('A file named "%s" already exists.', Path::canonicalize($file->getPathname())));
    }
}
