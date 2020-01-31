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
use SensioLabs\Deptrac\Dependency\Result;

class ResultTest extends TestCase
{
    public function testAddDependency(): void
    {
        $classA = ClassLikeName::fromString('A');
        $classB = ClassLikeName::fromString('B');
        $classC = ClassLikeName::fromString('C');

        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency($classA, $classB, new FileOccurrence(new AstFileReference('a.php'), 12)));
        $dependencyResult->addDependency($dep2 = new Dependency($classB, $classC, new FileOccurrence(new AstFileReference('b.php'), 12)));
        $dependencyResult->addDependency($dep3 = new Dependency($classA, $classC, new FileOccurrence(new AstFileReference('a.php'), 12)));
        static::assertSame([$dep1, $dep3], $dependencyResult->getDependenciesByClass($classA));
        static::assertSame([$dep2], $dependencyResult->getDependenciesByClass($classB));
        static::assertSame([], $dependencyResult->getDependenciesByClass($classC));
        static::assertCount(3, $dependencyResult->getDependenciesAndInheritDependencies());
    }

    public function testGetDependenciesAndInheritDependencies(): void
    {
        $classA = ClassLikeName::fromString('A');
        $classB = ClassLikeName::fromString('B');

        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency($classA, $classB, new FileOccurrence(new AstFileReference('a.php'), 12)));
        $dependencyResult->addInheritDependency($dep2 = new InheritDependency($classA, $classB, $dep1, AstInherit::newExtends($classB, new FileOccurrence(new AstFileReference('a.php'), 12))));
        static::assertEquals([$dep1, $dep2], $dependencyResult->getDependenciesAndInheritDependencies());
    }
}
