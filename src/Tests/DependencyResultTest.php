<?php

namespace DependencyTracker\Tests;

use DependencyTracker\DependencyResult;

class DependencyResultTest extends \PHPUnit_Framework_TestCase
{
    public function testAddDependency()
    {
        $dependencyResult = new DependencyResult();
        $dependencyResult->addDependency($dep1 = new DependencyResult\Dependency('A', 12, 'B'));
        $dependencyResult->addDependency($dep2 = new DependencyResult\Dependency('B', 12, 'C'));
        $dependencyResult->addDependency($dep3 = new DependencyResult\Dependency('A', 12, 'C'));
        $this->assertSame([$dep1, $dep3], $dependencyResult->getDependenciesByClass('A'));
        $this->assertSame([$dep2], $dependencyResult->getDependenciesByClass('B'));
        $this->assertSame([], $dependencyResult->getDependenciesByClass('C'));
        $this->assertCount(3, $dependencyResult->getDependenciesAndInheritDependencies());
    }

    public function testGetDependenciesAndInheritDependencies()
    {
        $dependencyResult = new DependencyResult();
        $dependencyResult->addDependency($dep1 = new DependencyResult\Dependency('A', 12, 'B'));
        $dependencyResult->addInheritDependency($dep2 = new DependencyResult\Dependency('A', 12, 'B'));
        $this->assertEquals([$dep1, $dep2], $dependencyResult->getDependenciesAndInheritDependencies());
    }
}
