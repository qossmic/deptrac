<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Ast;

use Symfony\Contracts\EventDispatcher\Event;

class PreCreateAstMapEvent extends Event
{
    public function __construct(private readonly int $expectedFileCount)
    {
    }

    public function getExpectedFileCount(): int
    {
        return $this->expectedFileCount;
    }
}
