<?php

namespace examples\Layer1;

use examples\Layer2\SomeOtherClass;

class SomeClass
{
    /**
     * @var SomeOtherClass
     */
    private $someOtherClass;

    /**
     * @param SomeOtherClass $someOtherClass
     */
    public function __construct(SomeOtherClass $someOtherClass)
    {
        $this->someOtherClass = $someOtherClass;
    }
}
