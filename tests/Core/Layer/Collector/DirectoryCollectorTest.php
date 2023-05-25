<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Layer\Collector\DirectoryCollector;

final class DirectoryCollectorTest extends TestCase
{
    private DirectoryCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new DirectoryCollector();
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['value' => 'foo/layer1/.*'], 'foo/layer1/bar.php', true];
        yield [['value' => 'foo/layer1/.*'], 'foo/layer1/dir/bar.php', true];
        yield [['value' => 'foo/layer1/.*'], 'foo/layer2/bar.php', false];
        yield [['value' => 'foo/layer2/.*'], 'foo\\layer2\\bar.php', true];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $filePath, bool $expected): void
    {
        $fileReferenceBuilder = FileReferenceBuilder::create($filePath);
        $fileReferenceBuilder->newClassLike('Test', [], false);
        $fileReference = $fileReferenceBuilder->build();

        $actual = $this->collector->satisfy(
            $configuration,
            $fileReference->classLikeReferences[0],
        );

        self::assertSame($expected, $actual);
    }

    public function testMissingRegexThrowsException(): void
    {
        $fileReferenceBuilder = FileReferenceBuilder::create('/some/path/to/file.php');
        $fileReferenceBuilder->newClassLike('Test', [], false);
        $fileReference = $fileReferenceBuilder->build();

        $this->expectException(InvalidCollectorDefinitionException::class);
        $this->expectExceptionMessage('DirectoryCollector needs the regex configuration.');

        $this->collector->satisfy(
            [],
            $fileReference->classLikeReferences[0],
        );
    }

    public function testInvalidRegexParam(): void
    {
        $fileReferenceBuilder = FileReferenceBuilder::create('/some/path/to/file.php');
        $fileReferenceBuilder->newClassLike('Test', [], false);
        $fileReference = $fileReferenceBuilder->build();

        $this->expectException(InvalidCollectorDefinitionException::class);

        $this->collector->satisfy(
            ['value' => '\\'],
            $fileReference->classLikeReferences[0],
        );
    }
}
