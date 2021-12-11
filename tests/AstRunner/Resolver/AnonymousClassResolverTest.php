<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyser;

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

        $filePath = __DIR__.'/Fixtures/AnonymousClass.php';
        $astFileReference = $parser->parseFile($filePath, ConfigurationAnalyser::fromArray([]));

        $astClassReferences = $astFileReference->getAstClassReferences();

        self::assertCount(3, $astClassReferences);
        self::assertCount(0, $astClassReferences[0]->getDependencies());
        self::assertCount(0, $astClassReferences[1]->getDependencies());
        self::assertCount(2, $astClassReferences[2]->getDependencies());

        $dependencies = $astClassReferences[2]->getDependencies();

        self::assertSame(
            'Tests\Qossmic\Deptrac\AstRunner\Resolver\Fixtures\ClassA',
            $dependencies[0]->getTokenName()->toString()
        );
        self::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilepath());
        self::assertSame(19, $dependencies[0]->getFileOccurrence()->getLine());
        self::assertSame('anonymous_class_extends', $dependencies[0]->getType());

        self::assertSame(
            'Tests\Qossmic\Deptrac\AstRunner\Resolver\Fixtures\InterfaceC',
            $dependencies[1]->getTokenName()->toString()
        );
        self::assertSame($filePath, $dependencies[1]->getFileOccurrence()->getFilepath());
        self::assertSame(19, $dependencies[1]->getFileOccurrence()->getLine());
        self::assertSame('anonymous_class_implements', $dependencies[1]->getType());
    }
}
