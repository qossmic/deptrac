<?php

declare(strict_types = 1);

namespace Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\Fixtures;

use Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\Fixtures\Attribute\MyAttribute;

#[MyAttribute]
#[Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\Fixtures\Attribute\MyAttribute]
#[MyAttribute(1234)]
#[MyAttribute(value: 1234)]
#[MyAttribute(MyAttribute::VALUE)]
#[MyAttribute(["key" => "value"])]
#[MyAttribute(100 + 200)]
class Thing
{
}

#[MyAttribute(1234), MyAttribute(5678)]
class AnotherThing
{
}

namespace Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\Fixtures\Attribute;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE)]
class MyAttribute
{
    const VALUE = 'value';

    private $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }
}

