<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use SensioLabs\Deptrac\Configuration\Exception\MissingFileException;
use SensioLabs\Deptrac\Configuration\Exception\ParsedYamlIsNotAnArrayException;
use SensioLabs\Deptrac\Configuration\Loader;

/**
 * @covers \SensioLabs\Deptrac\Configuration\Loader
 */
final class LoaderTest extends TestCase
{
    public function testLoadThrowsMissingFileExceptionWhenFileDoesNotExist(): void
    {
        $file = __DIR__.'/../../examples/non-existent-file.yml';

        $loader = new Loader();

        self::expectException(MissingFileException::class);

        $loader->load($file);
    }

    public function testLoadThrowsFileCannotBeParsedAsYamlExceptionWhenFileDoesNotContainYaml(): void
    {
        $file = __FILE__;

        $loader = new Loader();

        self::expectException(FileCannotBeParsedAsYamlException::class);

        $loader->load($file);
    }

    public function testLoadThrowsParsedYamlIsNotAnArrayExceptionWhenFileDoesNotContainYamlThatCanBeParsedToAnArray(): void
    {
        $file = __DIR__.'/Fixtures/does-not-contain-array.yml';

        $loader = new Loader();

        self::expectException(ParsedYamlIsNotAnArrayException::class);

        $loader->load($file);
    }
}
