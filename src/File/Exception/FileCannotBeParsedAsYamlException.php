<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\File\Exception;

use Qossmic\Deptrac\Exception\ExceptionInterface;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;

final class FileCannotBeParsedAsYamlException extends RuntimeException implements ExceptionInterface
{
    public static function fromFilenameAndException(string $filename, ParseException $exception): self
    {
        return new self(sprintf(
            'File "%s" cannot be parsed as YAML: %s',
            $filename,
            $exception->getMessage()
        ));
    }
}
