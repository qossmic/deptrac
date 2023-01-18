<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\OutputFormatter;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;

class OutputException extends RuntimeException implements ExceptionInterface
{
    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
