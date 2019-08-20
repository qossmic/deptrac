<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration\Exception;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;

/**
 * @covers \SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException
 */
final class FileCannotBeParsedAsYamlExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new FileCannotBeParsedAsYamlException();

        self::assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testFromFilenameReturnsException(): void
    {
        $filename = __FILE__;

        $exception = FileCannotBeParsedAsYamlException::fromFilename($filename);

        $message = sprintf(
            'File "%s" cannot be parsed as YAML.',
            $filename
        );

        self::assertSame($message, $exception->getMessage());
    }
}
