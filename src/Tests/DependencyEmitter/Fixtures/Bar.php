<?php

namespace Foo;

use Baz\Bar as Baz;

class Foo
{
    /**
     * @var \Foo\Foo
     */
    protected $bar;

    /**
     * @var Foo
     */
    protected $foo;

    /**
     * @var Baz
     */
    protected $baz;

    /**
     * @param string $param1
     * @param Foo $param2
     * @param \Foo\Foo $param3
     * @param Baz $param4
     */
    public function baz($param1, $param2, $param3, $param4)
    {
    }
}
