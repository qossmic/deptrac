<?php

namespace Tests\SensioLabs\Deptrac\AstRunner;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceA;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceD;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceE;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceD;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceE;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseA;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseB;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseC;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA1;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA2;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceC;

class InheritanceDependencyVisitorTest extends TestCase
{
    use ArrayAsserts;

    /**
     * @param $fixture
     *
     * @return AstMap
     */
    private function getAstMap($fixture)
    {
        return (new AstRunner(new EventDispatcher()))->createAstMapByFiles(
            new NikicPhpParser(),
            [new \SplFileInfo(__DIR__.'/Fixtures/BasicInheritance/'.$fixture.'.php')]
        );
    }

    private function getInheritedInherits($class, AstMap $astMap)
    {
        return array_values(
            array_map(
                function (AstMap\FlattenAstInherit $v) {
                    return $v->__toString();
                },
                array_filter(
                    $astMap->getClassInherits($class),
                    function (AstMap\AstInheritInterface $v) {
                        return $v instanceof AstMap\FlattenAstInherit;
                    }
                )
            )
        );
    }

    public function testBasicInheritance()
    {
        $astMap = $this->getAstMap('FixtureBasicInheritance');

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceA::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceB::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceC::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceD::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceD::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceD::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceD::9 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceE::class, $astMap)
        );
    }

    public function testBasicInheritanceInterfaces()
    {
        $astMap = $this->getAstMap('FixtureBasicInheritanceInterfaces');

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceA::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceB::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceC::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceD::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceE::class, $astMap)
        );
    }

    public function testBasicMultipleInheritanceInterfaces()
    {
        $astMap = $this->getAstMap('MultipleInheritanceInterfaces');

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA1::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA2::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA1::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA2::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Extends))',
            ],
            $this->getInheritedInherits(MultipleInteritanceB::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA1::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA1::8 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB::9 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA2::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB::9 (Extends))',
            ],
            $this->getInheritedInherits(MultipleInteritanceC::class, $astMap)
        );
    }

    public function testBasicMultipleInheritanceWithNoise()
    {
        $astMap = $this->getAstMap('FixtureBasicInheritanceWithNoise');

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseA::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseB::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseA::18 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseB::19 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseC::class, $astMap)
        );
    }
}
