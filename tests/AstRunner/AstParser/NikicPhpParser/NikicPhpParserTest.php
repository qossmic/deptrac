<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Parser;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
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
            $this->createMock(TypeResolver::class)
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
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            $typeResolver
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
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            $typeResolver
        );

        $filePath = __DIR__.'/Fixtures/Attributes.php';
        $astFileReference = $parser->parseFile($filePath);
        $astClassReferences = $astFileReference->getAstClassReferences();
        self::assertCount(7, $astClassReferences[0]->getDependencies());
        self::assertCount(2, $astClassReferences[1]->getDependencies());
        self::assertCount(1, $astClassReferences[2]->getDependencies());
    }
}
