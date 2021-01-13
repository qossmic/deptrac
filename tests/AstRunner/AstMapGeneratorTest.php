<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Visitor;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use SensioLabs\Deptrac\AstRunner\Resolver\AnnotationDependencyResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\SensioLabs\Deptrac\AstRunner\ArrayAsserts;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassB;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassC;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitA;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitB;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitC;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitClass;
use Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitD;

final class AstMapGeneratorTest extends TestCase
{
    use ArrayAsserts;

    private function getAstMap(string $fixture): AstMap
    {
        $typeResolver = new TypeResolver();
        $astRunner = new AstRunner(
            new EventDispatcher(),
            new NikicPhpParser(
                ParserFactory::createParser(),
                new AstFileReferenceInMemoryCache(),
                $typeResolver,
                new AnnotationDependencyResolver($typeResolver),
                new AnonymousClassResolver(),
                new ClassConstantResolver()
            )
        );

        return $astRunner->createAstMapByFiles([$fixture]);
    }

    public function testBasicDependencyClass(): void
    {
        $astMap = $this->getAstMap(__DIR__.'/Fixtures/BasicDependency/BasicDependencyClass.php');

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassA::9 (Extends)',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::9 (Implements)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceByClassName(ClassLikeName::fromFQCN(BasicDependencyClassB::class)))
        );

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::13 (Implements)',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyClassInterfaceB::13 (Implements)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceByClassName(ClassLikeName::fromFQCN(BasicDependencyClassC::class)))
        );
    }

    public function testBasicTraitsClass(): void
    {
        $astMap = $this->getAstMap(__DIR__.'/Fixtures/BasicDependency/BasicDependencyTraits.php');

        self::assertArrayValuesEquals(
            [],
            $this->getInheritsAsString($astMap->getClassReferenceByClassName(ClassLikeName::fromFQCN(BasicDependencyTraitA::class)))
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritsAsString($astMap->getClassReferenceByClassName(ClassLikeName::fromFQCN(BasicDependencyTraitB::class)))
        );

        self::assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitB::7 (Uses)'],
            $this->getInheritsAsString($astMap->getClassReferenceByClassName(ClassLikeName::fromFQCN(BasicDependencyTraitC::class)))
        );

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitA::10 (Uses)',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitB::11 (Uses)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceByClassName(ClassLikeName::fromFQCN(BasicDependencyTraitD::class)))
        );

        self::assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency\BasicDependencyTraitA::15 (Uses)'],
            $this->getInheritsAsString($astMap->getClassReferenceByClassName(ClassLikeName::fromFQCN(BasicDependencyTraitClass::class)))
        );
    }

    public function testIssue319(): void
    {
        $astMap = $this->getAstMap(__DIR__.'/Fixtures/Issue319.php');

        self::assertSame(
            [
                'Foo\Exception',
                'Foo\RuntimeException',
                'LogicException',
            ],
            array_map(
                static function (AstMap\AstDependency $dependency) {
                    return $dependency->getClassLikeName()->toString();
                },
                $astMap->getAstFileReferences()[__DIR__.'/Fixtures/Issue319.php']->getDependencies()
            )
        );
    }

    /**
     * @return string[]
     */
    private function getInheritsAsString(?AstMap\AstClassReference $classReference): array
    {
        if (null === $classReference) {
            return [];
        }

        return array_map('strval', $classReference->getInherits());
    }
}
