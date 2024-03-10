<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstLoader;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\AnnotationReferenceExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\AnonymousClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassConstantExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\GroupUseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\KeywordExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\UseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanContainerDecorator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassB;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassC;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitA;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitB;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitC;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitClass;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitD;

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
                [
                    new AnnotationReferenceExtractor($this->createMock(PhpStanContainerDecorator::class), $typeResolver),
                    new AnonymousClassExtractor(),
                    new ClassConstantExtractor(),
                    new KeywordExtractor($typeResolver),
                    new ClassExtractor(),
                    new UseExtractor(),
                    new GroupUseExtractor(),
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
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassA::9 (Extends)',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::9 (Implements)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyClassB::class)))
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::13 (Implements)',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceB::13 (Implements)',
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
            ['Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitB::7 (Uses)'],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitC::class)))
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitA::10 (Uses)',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitB::11 (Uses)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitD::class)))
        );

        self::assertArrayValuesEquals(
            ['Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitA::15 (Uses)'],
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
                    return $dependency->token->toString();
                },
                $astMap->getFileReferences()[__DIR__.'/Fixtures/Issue319.php']->dependencies
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

        return array_map('strval', $classReference->inherits);
    }
}
