<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Ast;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;
use Throwable;

class CouldNotParseFileException extends RuntimeException implements ExceptionInterface
{
    public static function because(string $reason, Throwable $previous): self
    {
        return new self($reason, 0, $previous);
    }
}
