<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\Ast;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;
use Throwable;
/**
 * Exception thrown in a collector when it cannot parse a file.
 */
final class CouldNotParseFileException extends RuntimeException implements ExceptionInterface
{
    public static function because(string $reason, Throwable $previous) : self
    {
        return new self($reason, 0, $previous);
    }
}
