<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PreCreateAstMapEvent extends Event
{
    private $expectedFileCount;

    public function __construct(int $expectedFileCount)
    {
        $this->expectedFileCount = $expectedFileCount;
    }

    public function getExpectedFileCount(): int
    {
        return $this->expectedFileCount;
    }
}
