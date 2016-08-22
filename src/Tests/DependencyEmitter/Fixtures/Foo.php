<?php

namespace Foo;
use SomeUse;

class Bar extends BarExtends implements BarInterface1, \BarInterface2 {

    use SomeTrait;

    public function foo(SomeParam $someParam, $lala)
    {
        new SomeClass();
        new \SomeOtherClass();

        $foo = function(SomeOtherParam $someOtherParam) {

        };

        assert ($foo instanceof SomeInstanceOf);
    }

}