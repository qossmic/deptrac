<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\FileParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use SplFileInfo;

class ClassConstantResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new NikicPhpParser(
            new FileParser(ParserFactory::createParser()),
            new AstFileReferenceInMemoryCache(),
            new ClassConstantResolver()
        );

        $filePath = __DIR__.'/fixtures/ClassConst.php';
        $astFileReference = $parser->parse(new SplFileInfo($filePath));

        $astClassReferences = $astFileReference->getAstClassReferences();

        static::assertCount(2, $astClassReferences);
        static::assertCount(0, $astClassReferences[0]->getDependencies());
        static::assertCount(1, $astClassReferences[1]->getDependencies());

        $dependencies = $astClassReferences[1]->getDependencies();
        static::assertSame(
            'Tests\SensioLabs\Deptrac\Integration\fixtures\ClassA',
            $dependencies[0]->getClass()
        );
        static::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilenpath());
        static::assertSame(15, $dependencies[0]->getFileOccurrence()->getLine());
        static::assertSame('const', $dependencies[0]->getType());
    }
}
