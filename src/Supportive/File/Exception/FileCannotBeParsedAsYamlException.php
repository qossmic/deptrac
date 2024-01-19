<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\File\Exception;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;
use DEPTRAC_202401\Symfony\Component\Yaml\Exception\ParseException;
/**
 * @internal
 */
final class FileCannotBeParsedAsYamlException extends RuntimeException implements ExceptionInterface
{
    public static function fromFilenameAndException(string $filename, ParseException $exception) : self
    {
        return new self(\sprintf('File "%s" cannot be parsed as YAML: %s', $filename, $exception->getMessage()));
    }
}
