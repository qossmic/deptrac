<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Parser;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstParser\AstFileReferenceCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;

final class NikicPhpParserTest extends TestCase
{
    /** @var NikicPhpParser */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new NikicPhpParser(
            $this->createMock(Parser::class),
            $this->createMock(AstFileReferenceCache::class),
            $this->createMock(TypeResolver::class)
        );
    }

    public function testParseWithInvalidData(): void
    {
        $this->expectException(\TypeError::class);
        $this->parser->parseFile(new \stdClass());
    }
}
