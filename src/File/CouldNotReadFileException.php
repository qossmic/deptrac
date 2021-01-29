<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\File;

final class CouldNotReadFileException extends \RuntimeException
{
    public static function fromFilename(string $filename): self
    {
        return new self(sprintf(
            'File "%s" cannot be read.',
            $filename
        ));
    }
}
