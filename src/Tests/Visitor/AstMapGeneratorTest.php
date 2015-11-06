<?php


namespace DependencyTracker\Tests\Visitor;


use DependencyTracker\AstMap;
use DependencyTracker\AstMapGenerator;
use DependencyTracker\DependencyResult;
use DependencyTracker\Tests\ArrayAsserts;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyClassA;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyClassB;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyClassC;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyClassInterfaceA;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyClassInterfaceB;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyTraitA;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyTraitB;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyTraitC;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyTraitClass;
use DependencyTracker\Tests\Visitor\Fixtures\BasicDependency\BasicDependencyTraitD;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class AstMapGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use ArrayAsserts;

    /**
     * @param $fixture
     * @return AstMap
     */
    private function getAstMap($fixture)
    {
        $files = iterator_to_array(
            (new Finder())->in(__DIR__.'/Fixtures/BasicDependency/')->name($fixture.'.php')->files()
        );

        (new AstMapGenerator(new EventDispatcher()))->createAstMapByFiles(
            $astMap = new AstMap(),
            $files
        );

        return $astMap;
    }

    public function testBasicDependencyClass()
    {
        $astMap = $this->getAstMap('BasicDependencyClass');

        $this->assertArrayValuesEquals(
            [BasicDependencyClassA::class, BasicDependencyClassInterfaceA::class],
            $astMap->getClassInherits(BasicDependencyClassB::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [BasicDependencyClassInterfaceA::class, BasicDependencyClassInterfaceB::class],
            $astMap->getClassInherits(BasicDependencyClassC::class, $astMap)
        );
    }

    public function testBasicTraitsClass()
    {
        $astMap = $this->getAstMap('BasicDependencyTraits');

        $this->assertArrayValuesEquals(
            [],
            $astMap->getClassInherits(BasicDependencyTraitA::class)
        );

        $this->assertArrayValuesEquals(
            [],
            $astMap->getClassInherits(BasicDependencyTraitB::class)
        );

        $this->assertArrayValuesEquals(
            [BasicDependencyTraitB::class],
            $astMap->getClassInherits(BasicDependencyTraitC::class)
        );

        $this->assertArrayValuesEquals(
            [BasicDependencyTraitA::class, BasicDependencyTraitB::class],
            $astMap->getClassInherits(BasicDependencyTraitD::class)
        );

        $this->assertArrayValuesEquals(
            [BasicDependencyTraitA::class],
            $astMap->getClassInherits(BasicDependencyTraitClass::class)
        );
    }
}
