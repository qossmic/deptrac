<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Ast;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstLoader;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Ast\Parser\AnnotationReferenceExtractor;
use Qossmic\Deptrac\Ast\Parser\AnonymousClassExtractor;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Ast\Parser\ClassConstantExtractor;
use Qossmic\Deptrac\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Ast\Parser\TypeResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyClassB;
use Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyClassC;
use Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitA;
use Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitB;
use Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitC;
use Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitClass;
use Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitD;

final class AstMapGeneratorTest extends TestCase
{
    use ArrayAssertionTrait;

    private function getAstMap(string $fixture): AstMap
    {
        $typeResolver = new TypeResolver();
        $astRunner = new AstLoader(
            new NikicPhpParser(
                (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
                new AstFileReferenceInMemoryCache(),
                $typeResolver,
                [
                    new AnnotationReferenceExtractor($typeResolver),
                    new AnonymousClassExtractor(),
                    new ClassConstantExtractor(),
                ]
            ),
            new EventDispatcher()
        );

        return $astRunner->createAstMap([$fixture]);
    }

    public function testBasicDependencyClass(): void
    {
        $astMap = $this->getAstMap(__DIR__.'/Fixtures/BasicDependency/BasicDependencyClass.php');

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyClassA::9 (Extends)',
                'Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::9 (Implements)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyClassB::class)))
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::13 (Implements)',
                'Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceB::13 (Implements)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyClassC::class)))
        );
    }

    public function testBasicTraitsClass(): void
    {
        $astMap = $this->getAstMap(__DIR__.'/Fixtures/BasicDependency/BasicDependencyTraits.php');

        self::assertArrayValuesEquals(
            [],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitA::class)))
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitB::class)))
        );

        self::assertArrayValuesEquals(
            ['Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitB::7 (Uses)'],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitC::class)))
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitA::10 (Uses)',
                'Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitB::11 (Uses)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitD::class)))
        );

        self::assertArrayValuesEquals(
            ['Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitA::15 (Uses)'],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitClass::class)))
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
                static function (DependencyToken $dependency) {
                    return $dependency->getToken()->toString();
                },
                $astMap->getFileReferences()[__DIR__.'/Fixtures/Issue319.php']->getDependencies()
            )
        );
    }

    /**
     * @return string[]
     */
    private function getInheritsAsString(?ClassLikeReference $classReference): array
    {
        if (null === $classReference) {
            return [];
        }

        return array_map('strval', $classReference->getInherits());
    }
}
