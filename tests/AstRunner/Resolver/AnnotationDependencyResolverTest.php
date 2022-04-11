<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\Resolver\AnnotationDependencyResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;

final class AnnotationDependencyResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $typeResolver = new TypeResolver();
        $parser = new NikicPhpParser(
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            new AnnotationDependencyResolver($typeResolver)
        );

        $filePath = __DIR__.'/Fixtures/AnnotationDependency.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->getAstClassReferences();
        $annotationDependency = $astClassReferences[0]->getDependencies();

        self::assertCount(2, $astClassReferences);
        self::assertCount(9, $annotationDependency);
        self::assertCount(0, $astClassReferences[1]->getDependencies());

        self::assertSame(
            'Tests\Qossmic\Deptrac\Integration\Fixtures\AnnotationDependencyChild',
            $annotationDependency[0]->getTokenName()->toString()
        );
        self::assertSame($filePath, $annotationDependency[0]->getFileOccurrence()->getFilepath());
        self::assertSame(16, $annotationDependency[0]->getFileOccurrence()->getLine());
        self::assertSame('variable', $annotationDependency[0]->getType());

        self::assertSame(
            'Tests\Qossmic\Deptrac\Integration\Fixtures\AnnotationDependencyChild',
            $annotationDependency[1]->getTokenName()->toString()
        );
        self::assertSame($filePath, $annotationDependency[1]->getFileOccurrence()->getFilepath());
        self::assertSame(30, $annotationDependency[1]->getFileOccurrence()->getLine());
        self::assertSame('variable', $annotationDependency[1]->getType());

        self::assertSame(
            'Tests\Qossmic\Deptrac\Integration\Fixtures\AnnotationDependencyChild',
            $annotationDependency[2]->getTokenName()->toString()
        );
        self::assertSame($filePath, $annotationDependency[2]->getFileOccurrence()->getFilepath());
        self::assertSame(33, $annotationDependency[2]->getFileOccurrence()->getLine());
        self::assertSame('variable', $annotationDependency[2]->getType());

        self::assertSame(
            'Symfony\Component\Console\Exception\RuntimeException',
            $annotationDependency[3]->getTokenName()->toString()
        );
        self::assertSame($filePath, $annotationDependency[3]->getFileOccurrence()->getFilepath());
        self::assertSame(36, $annotationDependency[3]->getFileOccurrence()->getLine());
        self::assertSame('variable', $annotationDependency[3]->getType());

        self::assertSame(
            'Symfony\Component\Finder\SplFileInfo',
            $annotationDependency[4]->getTokenName()->toString()
        );
        self::assertSame($filePath, $annotationDependency[4]->getFileOccurrence()->getFilepath());
        self::assertSame(21, $annotationDependency[4]->getFileOccurrence()->getLine());
        self::assertSame('parameter', $annotationDependency[4]->getType());

        self::assertSame(
            'Tests\Qossmic\Deptrac\Integration\Fixtures\AnnotationDependencyChild',
            $annotationDependency[5]->getTokenName()->toString()
        );
        self::assertSame($filePath, $annotationDependency[5]->getFileOccurrence()->getFilepath());
        self::assertSame(21, $annotationDependency[5]->getFileOccurrence()->getLine());
        self::assertSame('returntype', $annotationDependency[5]->getType());
    }
}
