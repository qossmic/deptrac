<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;

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

        self::assertSame($classLikeNameA, $dependency->getClassLikeNameA());
        self::assertSame($classLikeNameB, $dependency->getClassLikeNameB());
        self::assertSame(1, $dependency->getFileOccurrence()->getLine());
        self::assertSame($dep, $dependency->getOriginalDependency());
        self::assertSame($astInherit, $dependency->getInheritPath());
    }
}
