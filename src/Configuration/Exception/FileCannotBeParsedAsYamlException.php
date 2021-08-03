<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration\Exception;

use RuntimeException;

final class FileCannotBeParsedAsYamlException extends RuntimeException
{
    public static function fromFilenameAndException(string $filename, \RuntimeException $exception): self
    {
        return new self(sprintf(
            'File "%s" cannot be parsed as YAML: %s',
            $filename,
            $exception->getMessage()
        ));
    }

    public static function fromFilename(string $filename): self
    {
        return new self(sprintf(
            'File "%s" cannot be parsed as YAML.',
            $filename
        ));
    }
}
