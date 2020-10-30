<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration\Loader;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\Exception\BaselineFileCannotBeReadException;
use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use SensioLabs\Deptrac\Configuration\Exception\FileDoesNotExistsException;
use SensioLabs\Deptrac\Configuration\Exception\ParsedYamlIsNotAnArrayException;
use SensioLabs\Deptrac\Configuration\Loader\YmlFileLoader;

/**
 * @covers \SensioLabs\Deptrac\Configuration\Loader
 */
final class YmlFileLoaderTest extends TestCase
{
    public function testLoadThrowsFileDoesNotExistsExceptionWhenFileDoesNotExist(): void
    {
        $file = __DIR__.'/../Fixtures/non-existent-file.yaml';

        $loader = new YmlFileLoader();

        $this->expectException(FileDoesNotExistsException::class);

        $loader->parseFile($file);
    }

    public function testLoadThrowsFileCannotBeParsedAsYamlExceptionWhenFileDoesNotContainYaml(): void
    {
        $file = __FILE__;

        $loader = new YmlFileLoader();

        $this->expectException(FileCannotBeParsedAsYamlException::class);

        $loader->parseFile($file);
    }

    public function testLoadThrowsParsedYamlIsNotAnArrayExceptionWhenFileDoesNotContainYamlThatCanBeParsedToAnArray(): void
    {
        $file = __DIR__.'/../Fixtures/does-not-contain-array.yml';

        $loader = new YmlFileLoader();

        $this->expectException(ParsedYamlIsNotAnArrayException::class);

        $loader->parseFile($file);
    }

    public function testLoadThrowsImportFileCannotBeReadException(): void
    {
        $file = __DIR__.'/../Fixtures/non-existent-baseline.yml';

        $loader = new YmlFileLoader();

        $this->expectException(BaselineFileCannotBeReadException::class);
        $this->expectExceptionMessageMatches('~non-existent-file\.yaml~');

        $loader->parseFile($file);
    }

    public function testLoadWithBaseline(): void
    {
        $file = __DIR__.'/../Fixtures/baseline-main.yml';

        $loader = new YmlFileLoader();
        $configuration = $loader->parseFile($file);
        self::assertSame([
            'DummyClass' => [
                'FooClass',
                'BarClass',
            ],
        ], $configuration['skip_violations']);
    }
}
