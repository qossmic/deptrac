<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;

final class AnonymousClassResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new NikicPhpParser(
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            new AnonymousClassResolver()
        );

        $filePath = __DIR__.'/fixtures/AnonymousClass.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->getAstClassReferences();

        self::assertCount(3, $astClassReferences);
        self::assertCount(0, $astClassReferences[0]->getDependencies());
        self::assertCount(0, $astClassReferences[1]->getDependencies());
        self::assertCount(2, $astClassReferences[2]->getDependencies());

        $dependencies = $astClassReferences[2]->getDependencies();

        self::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\fixtures\ClassA',
            $dependencies[0]->getClassLikeName()->toString()
        );
        self::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilepath());
        self::assertSame(19, $dependencies[0]->getFileOccurrence()->getLine());
        self::assertSame('anonymous_class_extends', $dependencies[0]->getType());

        self::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\fixtures\InterfaceC',
            $dependencies[1]->getClassLikeName()->toString()
        );
        self::assertSame($filePath, $dependencies[1]->getFileOccurrence()->getFilepath());
        self::assertSame(19, $dependencies[1]->getFileOccurrence()->getLine());
        self::assertSame('anonymous_class_implements', $dependencies[1]->getType());
    }
}
