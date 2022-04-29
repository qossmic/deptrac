<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\File\Exception;

use Qossmic\Deptrac\Exception\ExceptionInterface;
use RuntimeException;

final class CouldNotReadFileException extends RuntimeException implements ExceptionInterface
{
    public static function fromFilename(string $filename): self
    {
        return new self(sprintf(
            'File "%s" cannot be read.',
            $filename
        ));
    }
}
