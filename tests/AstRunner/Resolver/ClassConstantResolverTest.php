<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;

final class ClassConstantResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new NikicPhpParser(
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            new ClassConstantResolver()
        );

        $filePath = __DIR__.'/fixtures/ClassConst.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->getAstClassReferences();

        self::assertCount(2, $astClassReferences);
        self::assertCount(0, $astClassReferences[0]->getDependencies());
        self::assertCount(1, $astClassReferences[1]->getDependencies());

        $dependencies = $astClassReferences[1]->getDependencies();
        self::assertSame(
            'Tests\SensioLabs\Deptrac\Integration\fixtures\ClassA',
            $dependencies[0]->getClassLikeName()->toString()
        );
        self::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilepath());
        self::assertSame(15, $dependencies[0]->getFileOccurrence()->getLine());
        self::assertSame('const', $dependencies[0]->getType());
    }
}
