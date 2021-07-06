<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyzer;

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

        $filePath = __DIR__.'/Fixtures/ClassConst.php';
        $astFileReference = $parser->parseFile($filePath, ConfigurationAnalyzer::fromArray([]));

        $astClassReferences = $astFileReference->getAstClassReferences();

        self::assertCount(2, $astClassReferences);
        self::assertCount(0, $astClassReferences[0]->getDependencies());
        self::assertCount(1, $astClassReferences[1]->getDependencies());

        $dependencies = $astClassReferences[1]->getDependencies();
        self::assertSame(
            'Tests\Qossmic\Deptrac\Integration\Fixtures\ClassA',
            $dependencies[0]->getTokenName()->toString()
        );
        self::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilepath());
        self::assertSame(15, $dependencies[0]->getFileOccurrence()->getLine());
        self::assertSame('const', $dependencies[0]->getType());
    }
}
