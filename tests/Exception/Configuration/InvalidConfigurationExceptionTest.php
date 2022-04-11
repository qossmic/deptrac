<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Exception\Configuration;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\Exception\InvalidConfigurationException;

/**
 * @covers \Qossmic\Deptrac\Configuration\Exception\InvalidConfigurationException
 */
final class InvalidConfigurationExceptionTest extends TestCase
{
    public function testIsInvalidArgumentException(): void
    {
        $exception = new InvalidConfigurationException();

        self::assertInstanceOf(InvalidArgumentException::class, $exception);
    }

    public function testFromDuplicateLayerNamesReturnsException(): void
    {
        $layerNames = [
            'foo',
            'bar',
        ];

        $exception = InvalidConfigurationException::fromDuplicateLayerNames(...$layerNames);

        natsort($layerNames);

        $message = sprintf(
            'Configuration can not contain multiple layers with the same name, got "%s" as duplicate.',
            implode('", "', $layerNames)
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testFromUnknownLayerNamesReturnsException(): void
    {
        $layerNames = [
            'foo',
            'bar',
        ];

        $exception = InvalidConfigurationException::fromUnknownLayerNames(...$layerNames);

        natsort($layerNames);

        $message = sprintf(
            'Configuration can not reference rule sets with unknown layer names, got "%s" as unknown.',
            implode('", "', $layerNames)
        );

        self::assertSame($message, $exception->getMessage());
    }
}
