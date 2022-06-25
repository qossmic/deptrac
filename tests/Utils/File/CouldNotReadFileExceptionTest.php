<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Utils\File;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Utils\File\Exception\CouldNotReadFileException;
use RuntimeException;

/**
 * @covers \Qossmic\Deptrac\Utils\File\Exception\CouldNotReadFileException
 */
final class CouldNotReadFileExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new CouldNotReadFileException();

        self::assertInstanceOf(RuntimeException::class, $exception);
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
