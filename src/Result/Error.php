<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Result;

/**
 * @psalm-immutable
 */
class Error
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function toString(): string
    {
        return $this->message;
    }
}
