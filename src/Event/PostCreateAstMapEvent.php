<?php

namespace DependencyTracker\Event;

use DependencyTracker\AstMap;
use Symfony\Component\EventDispatcher\Event;

class PostCreateAstMapEvent extends Event
{
    protected $astMap;

    /**
     * PreCreateAstMap constructor.
     * @param $astMap
     */
    public function __construct(AstMap $astMap)
    {
        $this->astMap = $astMap;
    }

    /**
     * @return AstMap
     */
    public function getAstMap()
    {
        return $this->astMap;
    }

}
