<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Collector\DirectoryCollector;
use SensioLabs\Deptrac\Collector\Registry;

class DirectoryCollectorTest extends TestCase
{
    public function testType(): void
    {
        static::assertEquals('directory', (new DirectoryCollector())->getType());
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['regex' => 'foo/layer1/.*'], 'foo/layer1/bar.php', true];
        yield [['regex' => 'foo/layer1/.*'], 'foo/layer1/dir/bar.php', true];
        yield [['regex' => 'foo/layer1/.*'], 'foo/layer2/bar.php', false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $filePath, bool $expected): void
    {
        $fileReference = $this->prophesize(AstFileReference::class);
        $fileReference->getFilepath()->willReturn($filePath);

        $astClassReference = $this->prophesize(AstClassReferenceInterface::class);
        $astClassReference->getFileReference()->willReturn($fileReference->reveal());

        $stat = (new DirectoryCollector())->satisfy(
            $configuration,
            $astClassReference->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        static::assertEquals($expected, $stat);
    }
}
