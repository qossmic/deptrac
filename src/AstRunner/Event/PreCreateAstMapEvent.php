<?php

namespace SensioLabs\Deptrac\AstRunner\Event;

use Symfony\Component\EventDispatcher\Event;

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
