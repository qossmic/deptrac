<?php

declare(strict_types = 1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\Fixtures;

use Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\Fixtures\Tests;

/**
 * @template Ta of AnotherThing
 */
class Thing
{
    /**
     * @template Tb of string
     */
    public function method(): void
    {
        /**
         * @var Tb $var
         */
        $var = '';
    }
}

class AnotherThing
{
}
