<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\Resolver\NameScope;

class NameScopeTest extends TestCase
{
    public function testResolveStringName(): void
    {
        $fileReference = new AstFileReference('baz.php');
        $fileReference->addDependency(AstDependency::useStmt('FooBar\OtherNamespace\OtherNamespaceClass', 1));
        $classReference = $fileReference->addClassReference('FooBar\Baz');

        $nameScope = new NameScope($classReference);

        static::assertSame('FooBar\SameNamespaceClass', $nameScope->resolveStringName('SameNamespaceClass'));
        static::assertSame('FooBar\OtherNamespace\OtherNamespaceClass', $nameScope->resolveStringName('OtherNamespaceClass'));
    }
}
