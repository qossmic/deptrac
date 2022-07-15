<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\Parser\AnnotationReferenceExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;

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
            ]
        );

        $filePath = __DIR__.'/Fixtures/AnnotationDependency.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->getClassLikeReferences();
        $annotationDependency = $astClassReferences[0]->getDependencies();

        self::assertCount(2, $astClassReferences);
        self::assertCount(9, $annotationDependency);
        self::assertCount(0, $astClassReferences[1]->getDependencies());

        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\AnnotationDependencyChild',
            $annotationDependency[0]->getToken()->toString()
        );
        self::assertSame($filePath, $annotationDependency[0]->getFileOccurrence()->getFilepath());
        self::assertSame(9, $annotationDependency[0]->getFileOccurrence()->getLine());
        self::assertSame('variable', $annotationDependency[0]->getType());

        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\AnnotationDependencyChild',
            $annotationDependency[1]->getToken()->toString()
        );
        self::assertSame($filePath, $annotationDependency[1]->getFileOccurrence()->getFilepath());
        self::assertSame(23, $annotationDependency[1]->getFileOccurrence()->getLine());
        self::assertSame('variable', $annotationDependency[1]->getType());

        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\AnnotationDependencyChild',
            $annotationDependency[2]->getToken()->toString()
        );
        self::assertSame($filePath, $annotationDependency[2]->getFileOccurrence()->getFilepath());
        self::assertSame(26, $annotationDependency[2]->getFileOccurrence()->getLine());
        self::assertSame('variable', $annotationDependency[2]->getType());

        self::assertSame(
            'Symfony\Component\Console\Exception\RuntimeException',
            $annotationDependency[3]->getToken()->toString()
        );
        self::assertSame($filePath, $annotationDependency[3]->getFileOccurrence()->getFilepath());
        self::assertSame(29, $annotationDependency[3]->getFileOccurrence()->getLine());
        self::assertSame('variable', $annotationDependency[3]->getType());

        self::assertSame(
            'Symfony\Component\Finder\SplFileInfo',
            $annotationDependency[4]->getToken()->toString()
        );
        self::assertSame($filePath, $annotationDependency[4]->getFileOccurrence()->getFilepath());
        self::assertSame(14, $annotationDependency[4]->getFileOccurrence()->getLine());
        self::assertSame('parameter', $annotationDependency[4]->getType());

        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\AnnotationDependencyChild',
            $annotationDependency[5]->getToken()->toString()
        );
        self::assertSame($filePath, $annotationDependency[5]->getFileOccurrence()->getFilepath());
        self::assertSame(14, $annotationDependency[5]->getFileOccurrence()->getLine());
        self::assertSame('returntype', $annotationDependency[5]->getType());
    }
}
