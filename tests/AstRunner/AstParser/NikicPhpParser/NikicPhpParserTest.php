<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\FileParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;

class NikicPhpParserTest extends TestCase
{
    /** @var NikicPhpParser */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new NikicPhpParser(
            $this->createMock(FileParser::class),
            $this->createMock(AstFileReferenceCache::class),
            $this->createMock(TypeResolver::class)
        );
    }

    public function testParseWithInvalidData(): void
    {
        $this->expectException(\TypeError::class);
        $this->parser->parse(new \stdClass());
    }
}
