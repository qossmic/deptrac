<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\Collector\DirectoryCollector;
use SensioLabs\Deptrac\Collector\Registry;

class DirectoryCollectorTest extends TestCase
{
    public function testType(): void
    {
        static::assertSame('directory', (new DirectoryCollector())->getType());
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
        $fileReference = new AstFileReference($filePath);
        $astClassReference = $fileReference->addClassReference(ClassLikeName::fromString('Test'));

        $stat = (new DirectoryCollector())->satisfy(
            $configuration,
            $astClassReference,
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal()
        );

        static::assertSame($expected, $stat);
    }

    public function testMissingRegexThrowsException(): void
    {
        $fileReference = new AstFileReference('/some/path/to/file.php');
        $astClassReference = $fileReference->addClassReference(ClassLikeName::fromString('Test'));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('DirectoryCollector needs the regex configuration.');

        (new DirectoryCollector())->satisfy(
            [],
            $astClassReference,
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal()
        );
    }
}
