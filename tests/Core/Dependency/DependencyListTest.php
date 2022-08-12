<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\DependencyList;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;

final class DependencyListTest extends TestCase
{
    public function testAddDependency(): void
    {
        $classA = ClassLikeToken::fromFQCN('A');
        $classB = ClassLikeToken::fromFQCN('B');
        $classC = ClassLikeToken::fromFQCN('C');

        $dependencyResult = new DependencyList();
        $dependencyResult->addDependency($dep1 = new Dependency($classA, $classB, new FileOccurrence('a.php', 12)));
        $dependencyResult->addDependency($dep2 = new Dependency($classB, $classC, new FileOccurrence('b.php', 12)));
        $dependencyResult->addDependency($dep3 = new Dependency($classA, $classC, new FileOccurrence('a.php', 12)));
        self::assertSame([$dep1, $dep3], $dependencyResult->getDependenciesByClass($classA));
        self::assertSame([$dep2], $dependencyResult->getDependenciesByClass($classB));
        self::assertSame([], $dependencyResult->getDependenciesByClass($classC));
        self::assertCount(3, $dependencyResult->getDependenciesAndInheritDependencies());
    }

    public function testGetDependenciesAndInheritDependencies(): void
    {
        $classA = ClassLikeToken::fromFQCN('A');
        $classB = ClassLikeToken::fromFQCN('B');

        $dependencyResult = new DependencyList();
        $dependencyResult->addDependency($dep1 = new Dependency($classA, $classB, new FileOccurrence('a.php', 12)));
        $dependencyResult->addInheritDependency($dep2 = new InheritDependency($classA, $classB, $dep1,
                                                                              new AstInherit(
                                                                                  $classB,
                                                                                  new FileOccurrence('a.php', 12),
                                                                                  AstInheritType::EXTENDS
                                                                              )
        ));
        self::assertSame([$dep1, $dep2], $dependencyResult->getDependenciesAndInheritDependencies());
    }
}
