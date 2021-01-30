<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Console\Command\Exception;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Console\Command\Exception\SingleDepfileIsRequiredException;

/**
 * @covers \Qossmic\Deptrac\Console\Command\Exception\SingleDepfileIsRequiredException
 */
final class SingleDepfileIsRequiredExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new SingleDepfileIsRequiredException();

        self::assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testFromFilenameReturnsException(): void
    {
        $argument = [];

        $exception = SingleDepfileIsRequiredException::fromArgument($argument);

        $message = sprintf(
            'Please specify a path to a depfile. Got "%s".',
            gettype($argument)
        );

        self::assertSame($message, $exception->getMessage());
    }
}
