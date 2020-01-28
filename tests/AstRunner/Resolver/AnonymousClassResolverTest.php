<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\FileParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use SplFileInfo;

class AnonymousClassResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new NikicPhpParser(
            new FileParser(ParserFactory::createParser()),
            new AstFileReferenceInMemoryCache(),
            [new AnonymousClassResolver()]
        );

        $filePath = __DIR__.'/fixtures/AnonymousClass.php';
        $astFileReference = $parser->parse(new SplFileInfo($filePath));

        $astClassReferences = $astFileReference->getAstClassReferences();

        static::assertCount(3, $astClassReferences);
        static::assertCount(0, $astClassReferences[0]->getDependencies());
        static::assertCount(0, $astClassReferences[1]->getDependencies());
        static::assertCount(2, $astClassReferences[2]->getDependencies());

        $dependencies = $astClassReferences[2]->getDependencies();

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\fixtures\ClassA',
            $dependencies[0]->getClass()
        );
        static::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilenpath());
        static::assertSame(19, $dependencies[0]->getFileOccurrence()->getLine());
        static::assertSame('anonymous_class_extends', $dependencies[0]->getType());

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\fixtures\InterfaceC',
            $dependencies[1]->getClass()
        );
        static::assertSame($filePath, $dependencies[1]->getFileOccurrence()->getFilenpath());
        static::assertSame(19, $dependencies[1]->getFileOccurrence()->getLine());
        static::assertSame('anonymous_class_implements', $dependencies[1]->getType());
    }
}
