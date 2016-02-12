<?php


namespace DependencyTracker\Tests;


use DependencyTracker\DependencyInheritanceFlatter;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
use DependencyTracker\DependencyResult\InheritDependency;
use Prophecy\Argument;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\FlattenAstInherit;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\AstClassReference;

class DependencyInheritanceFlatterTest extends \PHPUnit_Framework_TestCase
{

    private function getAstReference($className)
    {
        $astClass = $this->prophesize(AstClassReference::class);
        $astClass->getClassName()->willReturn($className);

        return $astClass->reveal();
    }

    private function getDependency($className)
    {
        $dep = $this->prophesize(Dependency::class);
        $dep->getClassA()->willReturn($className);
        return $dep->reveal();
    }

    public function testFlattenDependencies()
    {
        $this->markTestIncomplete('check test');

        $astMap = $this->prophesize(AstMap::class);
        $astMap->getAstClassReferences()->willReturn([
            $astRef1 = $this->getAstReference('A'),
        ]);
        $astMap->getClassInherits('C')->willReturn([
            $inherit1 = $this->prophesize(FlattenAstInherit::class)->reveal()
        ]);

        $inheritDependency = new InheritDependency('A', $inherit1);

        $result = $this->prophesize(DependencyResult::class);
        $result->getDependenciesByClass('A')->willReturn([
            $dependency1 = $this->getDependency('C')
        ]);
        $result->addInheritDependency($inheritDependency)->shouldBeCalled();

        (new DependencyInheritanceFlatter())->flattenDependencies($astMap->reveal(), $result->reveal());

    }

}
