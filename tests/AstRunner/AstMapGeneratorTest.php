<?php

namespace Tests\SensioLabs\Deptrac\AstRunner\Visitor;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\SensioLabs\Deptrac\AstRunner\ArrayAsserts;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassB;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassC;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitA;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitB;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitC;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitClass;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitD;

class AstMapGeneratorTest extends TestCase
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
            [new \SplFileInfo(__DIR__.'/Fixtures/BasicDependency/'.$fixture.'.php')]
        );
    }

    private function getDirectInherits($class, AstMap $astMap)
    {
        return array_map(
            function (AstMap\AstInherit $v) {
                return $v->__toString();
            },
            array_filter(
                $astMap->getClassInherits($class),
                function (AstMap\AstInheritInterface $v) {
                    return $v instanceof AstMap\AstInherit;
                }
            )
        );
    }

    public function testBasicDependencyClass()
    {
        $astMap = $this->getAstMap('BasicDependencyClass');

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassA::9 (Extends)',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::9 (Implements)',
            ],
            $this->getDirectInherits(BasicDependencyClassB::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::13 (Implements)',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassInterfaceB::13 (Implements)',
            ],
            $this->getDirectInherits(BasicDependencyClassC::class, $astMap)
        );
    }

    public function testBasicTraitsClass()
    {
        $astMap = $this->getAstMap('BasicDependencyTraits');

        $this->assertArrayValuesEquals(
            [],
            $this->getDirectInherits(BasicDependencyTraitA::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [],
            $this->getDirectInherits(BasicDependencyTraitB::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitB::7 (Uses)'],
            $this->getDirectInherits(BasicDependencyTraitC::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitA::10 (Uses)',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitB::11 (Uses)',
            ],
            $this->getDirectInherits(BasicDependencyTraitD::class, $astMap)
        );

        $this->assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitA::15 (Uses)'],
            $this->getDirectInherits(BasicDependencyTraitClass::class, $astMap)
        );
    }
}
