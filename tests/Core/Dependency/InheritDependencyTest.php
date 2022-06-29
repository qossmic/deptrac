<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;

final class InheritDependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $classLikeNameA = ClassLikeToken::fromFQCN('a');
        $classLikeNameB = ClassLikeToken::fromFQCN('b');
        $fileOccurrence = FileOccurrence::fromFilepath('a.php', 1);

        $dependency = new InheritDependency(
            $classLikeNameA,
            $classLikeNameB,
            $dep = new Dependency($classLikeNameA, $classLikeNameB, $fileOccurrence),
            $astInherit = AstInherit::newExtends($classLikeNameB, $fileOccurrence)
        );

        self::assertSame($classLikeNameA, $dependency->getDepender());
        self::assertSame($classLikeNameB, $dependency->getDependent());
        self::assertSame(1, $dependency->getFileOccurrence()->getLine());
        self::assertSame($dep, $dependency->getOriginalDependency());
        self::assertSame($astInherit, $dependency->getInheritPath());
    }
}
