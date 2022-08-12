<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

/**
 * @psalm-immutable
 */
class Error
{
    public function __construct(private readonly string $message)
    {
    }

    public function toString(): string
    {
        return $this->message;
    }
}
