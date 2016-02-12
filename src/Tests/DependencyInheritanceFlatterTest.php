<?php


namespace DependencyTracker\Tests;


use DependencyTracker\DependencyInheritanceFlatter;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
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

    private function getDependency($className, $line)
    {
        $dep = $this->prophesize(Dependency::class);
        $dep->getClassA()->willReturn($className);
        $dep->getClassALine()->willReturn($line);
        return $dep->reveal();
    }

    public function testFlattenDependencies()
    {
        $astMap = $this->prophesize(AstMap::class);
        $astMap->getAstClassReferences()->willReturn([
            $astRef1 = $this->getAstReference('A'),
            $astRef2 = $this->getAstReference('B'),
        ]);
        $astMap->getClassInherits('A')->willReturn([
            $inherit1 = $this->prophesize(FlattenAstInherit::class)->reveal()
        ]);

        $result = $this->prophesize(DependencyResult::class);
        $result->getDependenciesByClass('A')->willReturn([
            $dependency1 = $this->getDependency('C', 1)
        ]);

        (new DependencyInheritanceFlatter())->flattenDependencies($astMap->reveal(), $result->reveal());

    }

}
