<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\KeywordExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;

final class ClassDocBlockExtractorTest extends TestCase
{
    private const EXPECTED = [
        ['Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\ClassDocBlockDependencySister', DependencyType::PARAMETER],
        ['Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\ClassDocBlockDependencyBrother', DependencyType::RETURN_TYPE],
        ['Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\ClassDocBlockDependencyChild', DependencyType::VARIABLE],
        ['Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\ClassDocBlockDependencySister', DependencyType::VARIABLE],
        ['Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\ClassDocBlockDependencyBrother', DependencyType::VARIABLE],
    ];

    public function testMethodResolving(): void
    {
        $typeResolver = new TypeResolver();
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            [
                new KeywordExtractor($typeResolver),
            ]
        );

        $filePath = __DIR__.'/Fixtures/ClassDocBlockDependency.php';
        $astFileReference = $parser->parseFile($filePath);

        $dependencies = $astFileReference->classLikeReferences[0]->dependencies;

        self::assertCount(5, $astFileReference->classLikeReferences[0]->dependencies);

        foreach ($dependencies as $key => $dependency) {
            self::assertSame(self::EXPECTED[$key][0], $dependency->token->toString());
            self::assertSame(self::EXPECTED[$key][1], $dependency->type);
        }
    }
}
