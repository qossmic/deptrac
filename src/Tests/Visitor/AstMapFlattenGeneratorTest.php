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
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class InheritanceDependencyVisitorTest extends \PHPUnit_Framework_TestCase
{

    use ArrayAsserts;

    /**
     * @param $fixture
     * @return AstMap
     */
    private function getAstMap($fixture)
    {
        $files = iterator_to_array(
            (new Finder())->in(__DIR__ . '/Fixtures')->name($fixture . '.php')->files()
        );

        (new AstMapGenerator(new EventDispatcher()))->createAstMapByFiles(
            $astMap = new AstMap(),
            $files
        );

        return $astMap;
    }

    public function testBasicInheritance()
    {
        $astMap = $this->getAstMap('FixtureBasicInheritance');

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceA::class)
        );

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceB::class)
        );

        $this->assertArrayValuesEquals(
            ['DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends)'],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceC::class)
        );

        $this->assertArrayValuesEquals(
            [
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends)'
            ],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceD::class)
        );

        $this->assertArrayValuesEquals(
            [
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends)'
            ],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceE::class)
        );

    }

    public function testBasicInheritanceInterfaces()
    {
        $astMap = $this->getAstMap('FixtureBasicInheritanceInterfaces');

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceInterfaceA::class)
        );

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceInterfaceB::class)
        );

        $this->assertArrayValuesEquals(
            ['DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Extends)'],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceInterfaceC::class)
        );

        $this->assertArrayValuesEquals(
            [
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Extends)'
            ],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceInterfaceD::class)
        );

        $this->assertArrayValuesEquals(
            [
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Extends)'
            ],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceInterfaceE::class)
        );

    }

    public function testBasicMultipleInheritanceInterfaces()
    {
        $astMap = $this->getAstMap('MultipleInheritanceInterfaces');

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(MultipleInteritanceA1::class)
        );

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(MultipleInteritanceA2::class)
        );

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(MultipleInteritanceA::class)
        );

        $this->assertArrayValuesEquals(
            [
                'DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA1::7 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA2::7 (Extends)'
            ],
            $astMap->getFlattenClassInherits(MultipleInteritanceB::class)
        );

        $this->assertArrayValuesEquals(
            [
                'DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA1::7 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA1::8 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA2::7 (Extends)',
                'DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceA::8 (Extends)'
            ],
            $astMap->getFlattenClassInherits(MultipleInteritanceC::class)
        );


    }

    public function testBasicMultipleInheritanceWithNoise()
    {
        $astMap = $this->getAstMap('FixtureBasicInheritanceWithNoise');

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceWithNoiseA::class)
        );

        $this->assertArrayValuesEquals(
            [],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceWithNoiseB::class)
        );

        $this->assertArrayValuesEquals(
            ['DependencyTracker\Tests\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseA::18 (Extends)'],
            $astMap->getFlattenClassInherits(FixtureBasicInheritanceWithNoiseC::class)
        );

    }

}
