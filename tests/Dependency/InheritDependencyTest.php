<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;

final class InheritDependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $classLikeNameA = ClassLikeName::fromFQCN('a');
        $classLikeNameB = ClassLikeName::fromFQCN('b');
        $fileOccurrence = FileOccurrence::fromFilepath('a.php', 1);

        $dependency = new InheritDependency(
            $classLikeNameA,
            $classLikeNameB,
            $dep = new Dependency($classLikeNameA, $classLikeNameB, $fileOccurrence),
            $astInherit = AstInherit::newExtends($classLikeNameB, $fileOccurrence)
        );

        static::assertSame($classLikeNameA, $dependency->getClassLikeNameA());
        static::assertSame($classLikeNameB, $dependency->getClassLikeNameB());
        static::assertSame(1, $dependency->getFileOccurrence()->getLine());
        static::assertSame($dep, $dependency->getOriginalDependency());
        static::assertSame($astInherit, $dependency->getInheritPath());
    }
}
