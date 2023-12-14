<?php

declare(strict_types = 1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\Fixtures;

/**
 * @deprecated for testing
 */
class DeprecatedClass extends UndeprecatedClass
{
    public function someMethod(AnotherThing $x) {}
}

class UndeprecatedClass
{
    #[\Deprecated]
    public function deprecatedMethod(Something $x) {}

    public function notDeprecated(AnotherThing $x) {}
}

class Something
{
}

class AnotherThing
{
}
