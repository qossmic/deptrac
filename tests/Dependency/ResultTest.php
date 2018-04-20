<?php

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyResult\Dependency;

class ResultTest extends TestCase
{
    public function testAddDependency()
    {
        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency('A', 12, 'B'));
        $dependencyResult->addDependency($dep2 = new Dependency('B', 12, 'C'));
        $dependencyResult->addDependency($dep3 = new Dependency('A', 12, 'C'));
        $this->assertSame([$dep1, $dep3], $dependencyResult->getDependenciesByClass('A'));
        $this->assertSame([$dep2], $dependencyResult->getDependenciesByClass('B'));
        $this->assertSame([], $dependencyResult->getDependenciesByClass('C'));
        $this->assertCount(3, $dependencyResult->getDependenciesAndInheritDependencies());
    }

    public function testGetDependenciesAndInheritDependencies()
    {
        $dependencyResult = new Result();
        $dependencyResult->addDependency($dep1 = new Dependency('A', 12, 'B'));
        $dependencyResult->addInheritDependency($dep2 = new Dependency('A', 12, 'B'));
        $this->assertEquals([$dep1, $dep2], $dependencyResult->getDependenciesAndInheritDependencies());
    }
}
