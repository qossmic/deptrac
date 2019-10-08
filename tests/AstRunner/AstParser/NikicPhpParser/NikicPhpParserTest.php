<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\FileParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class NikicPhpParserTest extends TestCase
{
    /** @var NikicPhpParser */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new NikicPhpParser(
            $this->createMock(FileParser::class),
            $this->createMock(AstFileReferenceCache::class)
        );
    }

    public function testSupport(): void
    {
        static::assertTrue($this->parser->supports(new \SplFileInfo('foo.php')));
        static::assertTrue($this->parser->supports(new \SplFileInfo('FOO.PHP')));
        static::assertFalse($this->parser->supports(new \SplFileInfo('FOO.html')));
        static::assertFalse($this->parser->supports(new \stdClass()));
    }

    public function testParseWithInvalidData(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('data not supported');
        $this->parser->parse(new \stdClass());
    }
}
