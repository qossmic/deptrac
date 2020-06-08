<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstParser\BetterReflection\Parser;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceA;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceB;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceC;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceD;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceE;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceA;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceB;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceC;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceD;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceE;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseA;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseB;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseC;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA1;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA2;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceB;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceC;

class AstMapFlattenGeneratorTest extends TestCase
{
    use ArrayAsserts;

    private function getAstMap(string $fixture): AstMap
    {
        $astRunner = new AstRunner(
            new EventDispatcher(),
            new Parser(new TypeResolver())
        );

        return $astRunner->createAstMapByFiles(
            [new \SplFileInfo(__DIR__.'/Fixtures/BasicInheritance/'.$fixture.'.php')]
        );
    }

    private function getInheritedInherits(string $class, AstMap $astMap): array
    {
        $inherits = [];
        foreach ($astMap->getClassInherits(ClassLikeName::fromFQCN($class)) as $v) {
            if (count($v->getPath()) > 0) {
                $inherits[] = (string) $v;
            }
        }

        return $inherits;
    }

    public function testBasicInheritance(): void
    {
        $astMap = $this->getAstMap('FixtureBasicInheritance');

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceA::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceB::class, $astMap)
        );

        static::assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceB::7 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceC::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceC::8 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceB::7 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceB::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceC::8 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceD::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceD::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceC::8 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceB::7 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceB::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceD::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceC::8 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceC::8 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceD::9 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceE::class, $astMap)
        );
    }

    public function testBasicInheritanceInterfaces(): void
    {
        $astMap = $this->getAstMap('FixtureBasicInheritanceInterfaces');

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceA::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceB::class, $astMap)
        );

        static::assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceB::7 (Implements))'],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceC::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceC::8 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceB::7 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceB::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceC::8 (Implements))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceD::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceC::8 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceD::9 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceB::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceD::9 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceC::8 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceD::9 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceC::8 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceInterfaceB::7 (Implements))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceE::class, $astMap)
        );
    }

    public function testBasicMultipleInheritanceInterfaces(): void
    {
        $astMap = $this->getAstMap('MultipleInheritanceInterfaces');

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA1::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA2::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA1::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA::8 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA2::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA::8 (Implements))',
            ],
            $this->getInheritedInherits(MultipleInteritanceB::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA1::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceB::9 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA::8 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA1::8 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceB::9 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA2::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceB::9 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA::8 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceA::8 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\MultipleInteritanceB::9 (Implements))',
            ],
            $this->getInheritedInherits(MultipleInteritanceC::class, $astMap)
        );
    }

    public function testBasicMultipleInheritanceWithNoise(): void
    {
        $astMap = $this->getAstMap('FixtureBasicInheritanceWithNoise');

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseA::class, $astMap)
        );

        static::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseB::class, $astMap)
        );

        static::assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseA::18 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseB::19 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseC::class, $astMap)
        );
    }
}
