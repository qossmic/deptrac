<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Dependency\Result;

class ResultTest extends TestCase
{
    public function testAddDependency(): void
    {
        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency('A', 12, 'B'));
        $dependencyResult->addDependency($dep2 = new Dependency('B', 12, 'C'));
        $dependencyResult->addDependency($dep3 = new Dependency('A', 12, 'C'));
        static::assertSame([$dep1, $dep3], $dependencyResult->getDependenciesByClass('A'));
        static::assertSame([$dep2], $dependencyResult->getDependenciesByClass('B'));
        static::assertSame([], $dependencyResult->getDependenciesByClass('C'));
        static::assertCount(3, $dependencyResult->getDependenciesAndInheritDependencies());
    }

    public function testGetDependenciesAndInheritDependencies(): void
    {
        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency('A', 12, 'B'));
        $dependencyResult->addInheritDependency($dep2 = new InheritDependency('A', 'B', $dep1, AstInherit::newExtends('B', 12)));
        static::assertEquals([$dep1, $dep2], $dependencyResult->getDependenciesAndInheritDependencies());
    }
}
