<?php

declare(strict_types = 1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\Fixtures {

    function untaggedFunction() {}

    /**
     * @param string $foo
     * @param string $bar
     */
    function taggedFunction(string $foo, string $bar) {}

}
