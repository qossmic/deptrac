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
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\AnonymousClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassConstantExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassMethodExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\GroupUseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\NewExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\PropertyExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\TraitUseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\UseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\VariableExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicTypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\ParserInterface;
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
    /**
     * @return list<array{ParserInterface}>
     */
    public static function createParser(): array
    {
        $typeResolver = new NikicTypeResolver();
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            [
                new AnonymousClassExtractor(),
                new ClassConstantExtractor(),
                new ClassExtractor(),
                new UseExtractor(),
                new GroupUseExtractor(),
                new TraitUseExtractor($typeResolver),
            ]
        );
        return [
            'Nikic Parser' => [$parser]
        ];
    }

    /**
     * @dataProvider createParser
     */
    public function testBasicDependencyClass(ParserInterface $parser): void
    {
        $astMap = $this->getAstMap($parser, __DIR__.'/Fixtures/BasicDependency/BasicDependencyClass.php');

        self::assertEqualsCanonicalizing(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassA::9 (Extends)',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::9 (Implements)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyClassB::class)))
        );

        self::assertEqualsCanonicalizing(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceA::13 (Implements)',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyClassInterfaceB::13 (Implements)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyClassC::class)))
        );
    }

    /**
     * @dataProvider createParser
     */
    public function testBasicTraitsClass(ParserInterface $parser): void
    {
        $astMap = $this->getAstMap($parser, __DIR__.'/Fixtures/BasicDependency/BasicDependencyTraits.php');

        self::assertEqualsCanonicalizing(
            [],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitA::class)))
        );

        self::assertEqualsCanonicalizing(
            [],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitB::class)))
        );

        self::assertEqualsCanonicalizing(
            ['Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitB::7 (Uses)'],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitC::class)))
        );

        self::assertEqualsCanonicalizing(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitA::10 (Uses)',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitB::11 (Uses)',
            ],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitD::class)))
        );

        self::assertEqualsCanonicalizing(
            ['Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitA::15 (Uses)'],
            $this->getInheritsAsString($astMap->getClassReferenceForToken(ClassLikeToken::fromFQCN(BasicDependencyTraitClass::class)))
        );
    }

    /**
     * @dataProvider createParser
     */
    public function testIssue319(ParserInterface $parser): void
    {
        $astMap = $this->getAstMap($parser, __DIR__.'/Fixtures/Issue319.php');

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

    private function getAstMap(ParserInterface $parser, string $fixture): AstMap
    {
        $astRunner    = new AstLoader(
            $parser,
            new EventDispatcher()
        );

        return $astRunner->createAstMap([$fixture]);
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
