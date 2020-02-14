<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Dependency\Result;

class ResultTest extends TestCase
{
    public function testAddDependency(): void
    {
        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency('A', 'B', new FileOccurrence(new AstFileReference('a.php'), 12)));
        $dependencyResult->addDependency($dep2 = new Dependency('B', 'C', new FileOccurrence(new AstFileReference('b.php'), 12)));
        $dependencyResult->addDependency($dep3 = new Dependency('A', 'C', new FileOccurrence(new AstFileReference('a.php'), 12)));
        static::assertSame([$dep1, $dep3], $dependencyResult->getDependenciesByClass('A'));
        static::assertSame([$dep2], $dependencyResult->getDependenciesByClass('B'));
        static::assertSame([], $dependencyResult->getDependenciesByClass('C'));
        static::assertCount(3, $dependencyResult->getDependenciesAndInheritDependencies());
    }

    public function testGetDependenciesAndInheritDependencies(): void
    {
        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency('A', 'B', new FileOccurrence(new AstFileReference('a.php'), 12)));
        $dependencyResult->addInheritDependency($dep2 = new InheritDependency('A', 'B', $dep1, AstInherit::newExtends('B', new FileOccurrence(new AstFileReference('a.php'), 12))));
        static::assertEquals([$dep1, $dep2], $dependencyResult->getDependenciesAndInheritDependencies());
    }
}
