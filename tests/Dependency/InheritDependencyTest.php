<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;

class InheritDependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $classA = ClassLikeName::fromString('a');
        $classB = ClassLikeName::fromString('b');

        $fileOccurrence = new FileOccurrence(new AstFileReference('a.php'), 1);
        $dependency = new InheritDependency(
            $classA,
            $classB,
            $dep = new Dependency($classA, $classB, $fileOccurrence),
            $astInherit = AstInherit::newExtends($classB, $fileOccurrence)
        );

        static::assertEquals('a', $dependency->getClassA());
        static::assertEquals('b', $dependency->getClassB());
        static::assertEquals(1, $dependency->getFileOccurrence()->getLine());
        static::assertEquals($dep, $dependency->getOriginalDependency());
        static::assertSame($astInherit, $dependency->getInheritPath());
    }
}
