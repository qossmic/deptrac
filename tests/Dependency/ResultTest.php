<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Dependency\Result;

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
