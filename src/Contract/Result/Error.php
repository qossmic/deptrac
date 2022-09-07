<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

use Stringable;

/**
 * @psalm-immutable
 */
class Error implements Stringable
{
    public function __construct(private readonly string $message)
    {
    }

    public function __toString()
    {
        return $this->message;
    }
}
