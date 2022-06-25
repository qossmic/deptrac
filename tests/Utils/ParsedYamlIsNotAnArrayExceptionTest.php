<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Utils;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Utils\File\Exception\ParsedYamlIsNotAnArrayException;
use RuntimeException;

/**
 * @covers \Qossmic\Deptrac\Utils\File\Exception\ParsedYamlIsNotAnArrayException
 */
final class ParsedYamlIsNotAnArrayExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new ParsedYamlIsNotAnArrayException();

        self::assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testFromFilenameReturnsException(): void
    {
        $filename = __FILE__;

        $exception = ParsedYamlIsNotAnArrayException::fromFilename($filename);

        $message = sprintf(
            'File "%s" can be parsed as YAML, but the result is not an array.',
            $filename
        );

        self::assertSame($message, $exception->getMessage());
    }
}
