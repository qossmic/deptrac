<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\Collector\DirectoryCollector;
use SensioLabs\Deptrac\Collector\Registry;

final class DirectoryCollectorTest extends TestCase
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
        $fileReferenceBuilder = AstMap\FileReferenceBuilder::create($filePath);
        $fileReferenceBuilder->newClassLike('Test');
        $fileReference = $fileReferenceBuilder->build();

        $stat = (new DirectoryCollector())->satisfy(
            $configuration,
            $fileReference->getAstClassReferences()[0],
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );

        static::assertSame($expected, $stat);
    }

    public function testMissingRegexThrowsException(): void
    {
        $fileReferenceBuilder = AstMap\FileReferenceBuilder::create('/some/path/to/file.php');
        $fileReferenceBuilder->newClassLike('Test');
        $fileReference = $fileReferenceBuilder->build();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('DirectoryCollector needs the regex configuration.');

        (new DirectoryCollector())->satisfy(
            [],
            $fileReference->getAstClassReferences()[0],
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }
}
