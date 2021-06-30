<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration\Exception;

use RuntimeException;

final class ParsedYamlIsNotAnArrayException extends RuntimeException
{
    public static function fromFilename(string $filename): self
    {
        return new self(sprintf(
            'File "%s" can be parsed as YAML, but the result is not an array.',
            $filename
        ));
    }
}
