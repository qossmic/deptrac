<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\File;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\File\CouldNotReadFileException;

/**
 * @covers \Qossmic\Deptrac\File\CouldNotReadFileException
 */
final class CouldNotReadFileExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new CouldNotReadFileException();

        self::assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testFromFilenameReturnsException(): void
    {
        $filename = __FILE__;

        $exception = CouldNotReadFileException::fromFilename($filename);

        $message = sprintf(
            'File "%s" cannot be read.',
            $filename
        );

        self::assertSame($message, $exception->getMessage());
    }
}
