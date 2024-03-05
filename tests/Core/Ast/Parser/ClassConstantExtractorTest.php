<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassConstantExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;

final class ClassConstantExtractorTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            [
                new ClassConstantExtractor(),
            ]
        );

        $filePath = __DIR__.'/Fixtures/ClassConst.php';
        $astFileReference = $parser->parseFile($filePath);

        $astClassReferences = $astFileReference->classLikeReferences;

        self::assertCount(2, $astClassReferences);
        self::assertCount(0, $astClassReferences[0]->dependencies);
        self::assertCount(1, $astClassReferences[1]->dependencies);

        $dependencies = $astClassReferences[1]->dependencies;
        self::assertSame(
            'Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures\ClassA',
            $dependencies[0]->token->toString()
        );
        self::assertSame($filePath, $dependencies[0]->fileOccurrence->filepath);
        self::assertSame(15, $dependencies[0]->fileOccurrence->line);
        self::assertSame('const', $dependencies[0]->type->value);
    }
}
