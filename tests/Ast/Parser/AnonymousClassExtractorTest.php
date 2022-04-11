<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Ast\Parser;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\Parser\AnonymousClassExtractor;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Ast\Parser\TypeResolver;

final class AnonymousClassExtractorTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            [
                new AnonymousClassExtractor(),
            ]
        );

        $filePath = __DIR__.'/Fixtures/AnonymousClass.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->getClassLikeReferences();

        self::assertCount(3, $astClassReferences);
        self::assertCount(0, $astClassReferences[0]->getDependencies());
        self::assertCount(0, $astClassReferences[1]->getDependencies());
        self::assertCount(2, $astClassReferences[2]->getDependencies());

        $dependencies = $astClassReferences[2]->getDependencies();

        self::assertSame(
            'Tests\Qossmic\Deptrac\AstRunner\Resolver\Fixtures\ClassA',
            $dependencies[0]->getToken()->toString()
        );
        self::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilepath());
        self::assertSame(19, $dependencies[0]->getFileOccurrence()->getLine());
        self::assertSame('anonymous_class_extends', $dependencies[0]->getType());

        self::assertSame(
            'Tests\Qossmic\Deptrac\AstRunner\Resolver\Fixtures\InterfaceC',
            $dependencies[1]->getToken()->toString()
        );
        self::assertSame($filePath, $dependencies[1]->getFileOccurrence()->getFilepath());
        self::assertSame(19, $dependencies[1]->getFileOccurrence()->getLine());
        self::assertSame('anonymous_class_implements', $dependencies[1]->getType());
    }
}
