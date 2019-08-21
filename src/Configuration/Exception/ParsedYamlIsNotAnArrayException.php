<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration\Exception;

final class ParsedYamlIsNotAnArrayException extends \RuntimeException
{
    public static function fromFilename(string $filename): self
    {
        return new self(sprintf(
            'File "%s" can be parsed as YAML, but the result is not an array.',
            $filename
        ));
    }
}
