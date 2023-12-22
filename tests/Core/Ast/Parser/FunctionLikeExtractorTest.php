<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\FunctionLikeExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;
use Tests\Qossmic\Deptrac\Core\Ast\ArrayAssertionTrait;

final class FunctionLikeExtractorTest extends TestCase
{
    use ArrayAssertionTrait;

    public function testPropertyDependencyResolving(): void
    {
        $typeResolver = new TypeResolver();
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            $typeResolver,
            [
                new FunctionLikeExtractor($typeResolver),
            ]
        );

        $filePath = __DIR__.'/Fixtures/MethodSignatures.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->classLikeReferences;

        self::assertCount(3, $astClassReferences);
        [$classA, $classB, $classC] = $astClassReferences;

        self::assertArrayValuesEquals(
            [],
            $this->getDependenciesAsString($classA)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\MethodSignaturesA::12 (returntype)',
            ],
            $this->getDependenciesAsString($classB)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\MethodSignaturesB::21 (parameter)',
                // NOTE: We are not yet tracking the call from MethodSignatureC::test()
                // to MethodSignatureA::foo().
            ],
            $this->getDependenciesAsString($classC)
        );
    }

    /**
     * @return string[]
     */
    private function getDependenciesAsString(?ClassLikeReference $classReference): array
    {
        if (null === $classReference) {
            return [];
        }

        return array_map(
            static function (DependencyToken $dependency) {
                return "{$dependency->token->toString()}::{$dependency->fileOccurrence->line} ({$dependency->type->value})";
            },
            $classReference->dependencies
        );
    }
}
