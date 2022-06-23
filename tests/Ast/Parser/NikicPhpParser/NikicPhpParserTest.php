<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Ast\Parser\NikicPhpParser;

use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\Parser\AnnotationReferenceExtractor;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Ast\Parser\TypeResolver;
use stdClass;
use TypeError;

final class NikicPhpParserTest extends TestCase
{
    private NikicPhpParser $parser;

    protected function setUp(): void
    {
        $this->parser = new NikicPhpParser(
            $this->createMock(Parser::class),
            new AstFileReferenceInMemoryCache(),
            $this->createMock(TypeResolver::class),
            []
        );
    }

    public function testParseWithInvalidData(): void
    {
        $this->expectException(TypeError::class);
        $this->parser->parseFile(new stdClass());
    }

    public function testParseDoesNotIgnoreUsesByDefault(): void
    {
        $typeResolver = new TypeResolver();
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            $typeResolver,
            []
        );

        $filePath = __DIR__.'/Fixtures/CountingUseStatements.php';
        self::assertCount(1, $parser->parseFile($filePath)->getDependencies());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testParseAttributes(): void
    {
        $typeResolver = new TypeResolver();
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            $typeResolver,
            []
        );

        $filePath = __DIR__.'/Fixtures/Attributes.php';
        $astFileReference = $parser->parseFile($filePath);
        $astClassReferences = $astFileReference->getClassLikeReferences();
        self::assertCount(7, $astClassReferences[0]->getDependencies());
        self::assertCount(2, $astClassReferences[1]->getDependencies());
        self::assertCount(1, $astClassReferences[2]->getDependencies());
    }

    public function testParseTemplateTypes(): void
    {
        $typeResolver = new TypeResolver();
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            $typeResolver,
            [new AnnotationReferenceExtractor($typeResolver)]
        );

        $filePath = __DIR__.'/Fixtures/TemplateTypes.php';
        $astFileReference = $parser->parseFile($filePath);
        $astClassReferences = $astFileReference->getClassLikeReferences();
        self::assertCount(0, $astClassReferences[0]->getDependencies());
    }
}
