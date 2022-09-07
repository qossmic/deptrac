<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;

final class InheritDependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $classLikeNameA = ClassLikeToken::fromFQCN('a');
        $classLikeNameB = ClassLikeToken::fromFQCN('b');
        $fileOccurrence = new FileOccurrence('a.php', 1);

        $dependency = new InheritDependency(
            $classLikeNameA,
            $classLikeNameB,
            $dep = new Dependency($classLikeNameA, $classLikeNameB, $fileOccurrence),
            $astInherit = new AstInherit($classLikeNameB, $fileOccurrence, AstInheritType::EXTENDS)
        );

        self::assertSame($classLikeNameA, $dependency->getDepender());
        self::assertSame($classLikeNameB, $dependency->getDependent());
        self::assertSame(1, $dependency->getFileOccurrence()->line);
        self::assertSame($dep, $dependency->originalDependency);
        self::assertSame($astInherit, $dependency->inheritPath);
    }
}
