<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration\Exception;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\Exception\FileDoesNotExistsException;

/**
 * @covers \SensioLabs\Deptrac\Configuration\Exception\FileDoesNotExistsException
 */
final class FileDoesNotExistsExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new FileDoesNotExistsException();

        self::assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testFromFilenameReturnsException(): void
    {
        $filename = __FILE__;

        $exception = FileDoesNotExistsException::fromFilename($filename);

        $message = sprintf(
            'File "%s" does not exist. Run "deptrac init" to create one.',
            $filename
        );

        self::assertSame($message, $exception->getMessage());
    }
}
