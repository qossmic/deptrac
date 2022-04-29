<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Exception\Console;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Console\Exception\InvalidArgumentException;
use RuntimeException;

/**
 * @covers \Qossmic\Deptrac\Console\Exception\InvalidArgumentException
 */
final class InvalidArgumentExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new InvalidArgumentException();

        self::assertInstanceOf(RuntimeException::class, $exception);
    }

    public function provideUnepxectedTypes(): iterable
    {
        yield 'Depfile argument is null' => [null, 'Please specify a path to a Depfile. Got "NULL".'];
        yield 'Depfile argument is a list' => [[], 'Please specify a path to a Depfile. Got "array".'];
    }

    /**
     * @dataProvider provideUnepxectedTypes
     */
    public function testInvalidDepfileType($argument, string $expectedExceptionMessage): void
    {
        $exception = InvalidArgumentException::invalidDepfileType($argument);

        self::assertSame($expectedExceptionMessage, $exception->getMessage());
    }
}
