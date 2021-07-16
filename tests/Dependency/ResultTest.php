<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\Dependency\Result;

final class ResultTest extends TestCase
{
    public function testAddDependency(): void
    {
        $classA = ClassLikeName::fromFQCN('A');
        $classB = ClassLikeName::fromFQCN('B');
        $classC = ClassLikeName::fromFQCN('C');

        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency($classA, $classB, FileOccurrence::fromFilepath('a.php', 12)));
        $dependencyResult->addDependency($dep2 = new Dependency($classB, $classC, FileOccurrence::fromFilepath('b.php', 12)));
        $dependencyResult->addDependency($dep3 = new Dependency($classA, $classC, FileOccurrence::fromFilepath('a.php', 12)));
        self::assertSame([$dep1, $dep3], $dependencyResult->getDependenciesByClass($classA));
        self::assertSame([$dep2], $dependencyResult->getDependenciesByClass($classB));
        self::assertSame([], $dependencyResult->getDependenciesByClass($classC));
        self::assertCount(3, $dependencyResult->getDependenciesAndInheritDependencies());
    }

    public function testGetDependenciesAndInheritDependencies(): void
    {
        $classA = ClassLikeName::fromFQCN('A');
        $classB = ClassLikeName::fromFQCN('B');

        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency($classA, $classB, FileOccurrence::fromFilepath('a.php', 12)));
        $dependencyResult->addInheritDependency($dep2 = new InheritDependency($classA, $classB, $dep1, AstInherit::newExtends($classB, FileOccurrence::fromFilepath('a.php', 12))));
        self::assertEquals([$dep1, $dep2], $dependencyResult->getDependenciesAndInheritDependencies());
    }
}
