<?php

declare(strict_types = 1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\Fixtures;

class UntaggedThing
{
    public function untaggedFunction() {}
}

/**
 * @internal
 * @note Note one
 * @note Note two
 */
class TaggedThing
{
}
