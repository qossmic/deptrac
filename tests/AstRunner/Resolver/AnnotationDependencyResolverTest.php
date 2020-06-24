<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\BetterReflection\Parser;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use SplFileInfo;

class AnnotationDependencyResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new Parser(new TypeResolver());

        $filePath = __DIR__.'/Fixtures/AnnotationDependency.php';
        $astFileReference = $parser->parse(new SplFileInfo($filePath));

        $astClassReferences = $astFileReference->getAstClassReferences();
        $annotationDependency = $astClassReferences[0]->getDependencies();

        static::assertCount(2, $astClassReferences);
        static::assertCount(8, $annotationDependency);
        static::assertCount(0, $astClassReferences[1]->getDependencies());

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures\AnnotationDependencyChild',
            $annotationDependency[0]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $annotationDependency[0]->getFileOccurrence()->getFilepath());
        static::assertSame(9, $annotationDependency[0]->getFileOccurrence()->getLine());
        static::assertSame('property', $annotationDependency[0]->getType());

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures\AnnotationDependencyChild',
            $annotationDependency[1]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $annotationDependency[1]->getFileOccurrence()->getFilepath());
        static::assertSame(14, $annotationDependency[1]->getFileOccurrence()->getLine());
        static::assertSame('property', $annotationDependency[1]->getType());

        static::assertSame(
            'Symfony\Component\Finder\SplFileInfo',
            $annotationDependency[2]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $annotationDependency[2]->getFileOccurrence()->getFilepath());
        static::assertSame(19, $annotationDependency[2]->getFileOccurrence()->getLine());
        static::assertSame('parameter', $annotationDependency[2]->getType());

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures\AnnotationDependencyChild',
            $annotationDependency[3]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $annotationDependency[3]->getFileOccurrence()->getFilepath());
        static::assertSame(19, $annotationDependency[3]->getFileOccurrence()->getLine());
        static::assertSame('returntype', $annotationDependency[3]->getType());

        static::assertSame(
            'Symfony\Component\Console\Exception\RuntimeException',
            $annotationDependency[4]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $annotationDependency[4]->getFileOccurrence()->getFilepath());
        static::assertSame(19, $annotationDependency[4]->getFileOccurrence()->getLine());
        static::assertSame('throw', $annotationDependency[4]->getType());

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures\AnnotationDependencyChild',
            $annotationDependency[5]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $annotationDependency[5]->getFileOccurrence()->getFilepath());
        static::assertSame(28, $annotationDependency[5]->getFileOccurrence()->getLine());
        static::assertSame('variable', $annotationDependency[5]->getType());

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures\AnnotationDependencyChild',
            $annotationDependency[6]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $annotationDependency[6]->getFileOccurrence()->getFilepath());
        static::assertSame(31, $annotationDependency[6]->getFileOccurrence()->getLine());
        static::assertSame('variable', $annotationDependency[6]->getType());

        static::assertSame(
            'Symfony\Component\Console\Exception\RuntimeException',
            $annotationDependency[7]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $annotationDependency[7]->getFileOccurrence()->getFilepath());
        static::assertSame(34, $annotationDependency[7]->getFileOccurrence()->getLine());
        static::assertSame('variable', $annotationDependency[7]->getType());
    }
}
