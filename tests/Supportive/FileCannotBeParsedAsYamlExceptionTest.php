<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Utils;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Supportive\File\Exception\FileCannotBeParsedAsYamlException;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @covers \Qossmic\Deptrac\Supportive\File\Exception\FileCannotBeParsedAsYamlException
 */
final class FileCannotBeParsedAsYamlExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new FileCannotBeParsedAsYamlException();

        self::assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testFromFilenameAndExceptionReturnsException(): void
    {
        $filename = __FILE__;

        $exception = FileCannotBeParsedAsYamlException::fromFilenameAndException($filename, new ParseException('abc'));

        $message = sprintf(
            'File "%s" cannot be parsed as YAML: abc',
            $filename
        );

        self::assertSame($message, $exception->getMessage());
    }
}
