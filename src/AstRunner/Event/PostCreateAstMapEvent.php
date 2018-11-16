<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Event;

use SensioLabs\Deptrac\AstRunner\AstMap;
use Symfony\Component\EventDispatcher\Event;

class PostCreateAstMapEvent extends Event
{
    private $astMap;

    public function __construct(AstMap $astMap)
    {
        $this->astMap = $astMap;
    }

    public function getAstMap(): AstMap
    {
        return $this->astMap;
    }
}
