<?php


namespace DependencyTracker\Tests\Visitor;


use DependencyTracker\AstMap;
use DependencyTracker\AstMapGenerator;
use DependencyTracker\DependencyResult;
use DependencyTracker\Tests\ArrayAsserts;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceA;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceB;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceC;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceD;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceE;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceD;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceE;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseA;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseB;
use DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseC;
use DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA;
use DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA1;
use DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA2;
use DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceB;
use DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceC;
use DependencyTracker\Visitor\BasicDependencyVisitor;
use DependencyTracker\Visitor\InheritanceDependencyVisitor;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class InheritanceDependencyVisitorTest extends \PHPUnit_Framework_TestCase
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
            (new Finder())->in(__DIR__.'/Fixtures')->name($fixture.'.php')->files()
        );

        (new AstMapGenerator(new EventDispatcher()))->createAstMapByFiles(
            $astMap = new AstMap(),
            $files
        );

        (new BasicDependencyVisitor($dependencyResult))->analyze($astMap);
        (new InheritanceDependencyVisitor())->flattenInheritanceDependencies($astMap, $dependencyResult);

        return $dependencyResult;
    }

    public function testBasicInheritance()
    {
        $dependencyResult = $this->getDependencyResultForFixture('FixtureBasicInheritance');

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(FixtureBasicInheritanceA::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(FixtureBasicInheritanceB::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [FixtureBasicInheritanceA::class],
            $this->getInheritDepsForClass(FixtureBasicInheritanceC::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [FixtureBasicInheritanceA::class, FixtureBasicInheritanceB::class],
            $this->getInheritDepsForClass(FixtureBasicInheritanceD::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [FixtureBasicInheritanceA::class, FixtureBasicInheritanceB::class, FixtureBasicInheritanceC::class],
            $this->getInheritDepsForClass(FixtureBasicInheritanceE::class, $dependencyResult)
        );

    }

    public function testBasicInheritanceInterfaces()
    {
        $dependencyResult = $this->getDependencyResultForFixture('FixtureBasicInheritanceInterfaces');

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(FixtureBasicInheritanceInterfaceA::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(FixtureBasicInheritanceInterfaceB::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [FixtureBasicInheritanceInterfaceA::class],
            $this->getInheritDepsForClass(FixtureBasicInheritanceInterfaceC::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [FixtureBasicInheritanceInterfaceA::class, FixtureBasicInheritanceInterfaceB::class],
            $this->getInheritDepsForClass(FixtureBasicInheritanceInterfaceD::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [FixtureBasicInheritanceInterfaceA::class, FixtureBasicInheritanceInterfaceB::class, FixtureBasicInheritanceInterfaceC::class],
            $this->getInheritDepsForClass(FixtureBasicInheritanceInterfaceE::class, $dependencyResult)
        );

    }

    public function testBasicMultipleInheritanceInterfaces()
    {
        $dependencyResult = $this->getDependencyResultForFixture('MultipleInheritanceInterfaces');

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(MultipleInteritanceA1::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(MultipleInteritanceA2::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(MultipleInteritanceA::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [MultipleInteritanceA2::class],
            $this->getInheritDepsForClass(MultipleInteritanceB::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [ MultipleInteritanceA2::class, MultipleInteritanceA::class, MultipleInteritanceA1::class],
            $this->getInheritDepsForClass(MultipleInteritanceC::class, $dependencyResult)
        );


    }

    public function testBasicMultipleInheritanceWithNoise()
    {
        $dependencyResult = $this->getDependencyResultForFixture('FixtureBasicInheritanceWithNoise');

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(FixtureBasicInheritanceWithNoiseA::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritDepsForClass(FixtureBasicInheritanceWithNoiseB::class, $dependencyResult)
        );

        $this->assertArrayValuesEquals(
            [FixtureBasicInheritanceWithNoiseA::class],
            $this->getInheritDepsForClass(FixtureBasicInheritanceWithNoiseC::class, $dependencyResult)
        );

    }

    private function getInheritDepsForClass($class, DependencyResult $dependencyResult) {
        $inheritDeps = array_filter($dependencyResult->getDependencies(), function(DependencyResult\Dependency $v) use ($class) {
            if ($class !== $v->getClassA()) {
                return false;
            }

            return $v instanceof DependencyResult\InheritDependency;
        });

        return array_values(array_map(function(DependencyResult\Dependency $dependency) {
            return $dependency->getClassB();
        }, $inheritDeps));
    }

}
