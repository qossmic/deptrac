<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\AnnotationReferenceExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\KeywordExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;

final class AnnotationReferenceExtractorTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $typeResolver = new TypeResolver();
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            [
                new AnnotationReferenceExtractor($typeResolver),
                new KeywordExtractor($typeResolver),
            ]
        );

        $filePath = __DIR__.'/Fixtures/AnnotationDependency.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->classLikeReferences;
        $annotationDependency = $astClassReferences[0]->dependencies;

        self::assertCount(2, $astClassReferences);
        self::assertCount(9, $annotationDependency);
        self::assertCount(0, $astClassReferences[1]->dependencies);

        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\AnnotationDependencyChild',
            $annotationDependency[0]->token->toString()
        );
        self::assertSame($filePath, $annotationDependency[0]->fileOccurrence->filepath);
        self::assertSame(9, $annotationDependency[0]->fileOccurrence->line);
        self::assertSame('variable', $annotationDependency[0]->type->value);

        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\AnnotationDependencyChild',
            $annotationDependency[1]->token->toString()
        );
        self::assertSame($filePath, $annotationDependency[1]->fileOccurrence->filepath);
        self::assertSame(23, $annotationDependency[1]->fileOccurrence->line);
        self::assertSame('variable', $annotationDependency[1]->type->value);

        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\AnnotationDependencyChild',
            $annotationDependency[2]->token->toString()
        );
        self::assertSame($filePath, $annotationDependency[2]->fileOccurrence->filepath);
        self::assertSame(26, $annotationDependency[2]->fileOccurrence->line);
        self::assertSame('variable', $annotationDependency[2]->type->value);

        self::assertSame(
            'Symfony\Component\Console\Exception\RuntimeException',
            $annotationDependency[3]->token->toString()
        );
        self::assertSame($filePath, $annotationDependency[3]->fileOccurrence->filepath);
        self::assertSame(29, $annotationDependency[3]->fileOccurrence->line);
        self::assertSame('variable', $annotationDependency[3]->type->value);

        self::assertSame(
            'Symfony\Component\Finder\SplFileInfo',
            $annotationDependency[4]->token->toString()
        );
        self::assertSame($filePath, $annotationDependency[4]->fileOccurrence->filepath);
        self::assertSame(14, $annotationDependency[4]->fileOccurrence->line);
        self::assertSame('parameter', $annotationDependency[4]->type->value);

        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\AnnotationDependencyChild',
            $annotationDependency[5]->token->toString()
        );
        self::assertSame($filePath, $annotationDependency[5]->fileOccurrence->filepath);
        self::assertSame(14, $annotationDependency[5]->fileOccurrence->line);
        self::assertSame('returntype', $annotationDependency[5]->type->value);
    }
}
