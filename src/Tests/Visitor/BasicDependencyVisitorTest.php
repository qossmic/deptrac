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
use DependencyTracker\Visitor\BasicDependencyVisitor;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class BasicDependencyVisitorTest extends \PHPUnit_Framework_TestCase
{
    use ArrayAsserts;

    /**
     * @param $fixture
     * @return DependencyResult
     */
    private function getDependencyResultForFixture($fixture)
    {
        $dependencyResult = new DependencyResult();

        $files = iterator_to_array(
            (new Finder())->in(__DIR__.'/Fixtures/BasicDependency/')->name($fixture.'.php')->files()
        );

        (new AstMapGenerator(new EventDispatcher()))->createAstMapByFiles(
            $astMap = new AstMap(),
            $files
        );

        (new BasicDependencyVisitor($dependencyResult))->analyze($astMap);

        return $dependencyResult;
    }

    public function testBasicDependencyClass()
    {
        $dependencyResult = $this->getDependencyResultForFixture('BasicDependencyClass');

        $this->assertArrayValuesEquals(
            [BasicDependencyClassA::class, BasicDependencyClassInterfaceA::class],
            $this->getDepsForClass(BasicDependencyClassB::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [BasicDependencyClassInterfaceA::class, BasicDependencyClassInterfaceB::class],
            $this->getDepsForClass(BasicDependencyClassC::class, $dependencyResult)
        );
    }

    public function testBasicTraitsClass()
    {
        $dependencyResult = $this->getDependencyResultForFixture('BasicDependencyTraits');

        $this->assertArrayValuesEquals(
            [],
            $this->getDepsForClass(BasicDependencyTraitA::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getDepsForClass(BasicDependencyTraitB::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [BasicDependencyTraitB::class],
            $this->getDepsForClass(BasicDependencyTraitC::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [BasicDependencyTraitA::class, BasicDependencyTraitB::class],
            $this->getDepsForClass(BasicDependencyTraitD::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [BasicDependencyTraitA::class],
            $this->getDepsForClass(BasicDependencyTraitClass::class, $dependencyResult)
        );
    }


    private function getDepsForClass($class, DependencyResult $dependencyResult)
    {
        $deps = array_filter(
            $dependencyResult->getDependencies(),
            function (DependencyResult\Dependency $v) use ($class) {
                if ($class !== $v->getClassA()) {
                    return false;
                }

                return !($v instanceof DependencyResult\InheritDependency);
            }
        );

        return array_values(
            array_map(
                function (DependencyResult\Dependency $dependency) {
                    return $dependency->getClassB();
                },
                $deps
            )
        );
    }
}
