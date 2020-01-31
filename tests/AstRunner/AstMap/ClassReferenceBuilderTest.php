<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\AstMap;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class ClassReferenceBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        ClassReferenceBuilder::create(new AstFileReference('foo.php'), 'Foo')
            ->extends('Bar', 12)
            ->implements('Baz', 12)
            ->build();
    }
}
