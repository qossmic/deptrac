<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Ast\Parser;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Ast\Parser\ClassConstantExtractor;
use Qossmic\Deptrac\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Ast\Parser\TypeResolver;

final class ClassConstantExtractorTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            [
                new ClassConstantExtractor(),
            ]
        );

        $filePath = __DIR__.'/Fixtures/ClassConst.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->getClassLikeReferences();

        self::assertCount(2, $astClassReferences);
        self::assertCount(0, $astClassReferences[0]->getDependencies());
        self::assertCount(1, $astClassReferences[1]->getDependencies());

        $dependencies = $astClassReferences[1]->getDependencies();
        self::assertSame(
            'Tests\Qossmic\Deptrac\Integration\Fixtures\ClassA',
            $dependencies[0]->getToken()->toString()
        );
        self::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilepath());
        self::assertSame(15, $dependencies[0]->getFileOccurrence()->getLine());
        self::assertSame('const', $dependencies[0]->getType());
    }
}