<?php

namespace DependencyTracker\Event;

use Symfony\Component\EventDispatcher\Event;

class PreCreateAstMapEvent extends Event
{
    protected $expectedFileCount;

    /**
     * PreCreateAstMapEvent constructor.
     * @param $expectedFileCount
     */
    public function __construct($expectedFileCount)
    {
        $this->expectedFileCount = $expectedFileCount;
    }

    /**
     * @return mixed
     */
    public function getExpectedFileCount()
    {
        return $this->expectedFileCount;
    }


}
