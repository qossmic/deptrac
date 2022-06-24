<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Exception;

use Psr\Container\ContainerExceptionInterface;
use Qossmic\Deptrac\Utils\ExceptionInterface;
use RuntimeException;
use function implode;

final class InvalidCollectorDefinitionException extends RuntimeException implements ExceptionInterface
{
    public static function missingType(): self
    {
        return new self('Could not resolve collector definition because of missing "type" field.');
    }

    /**
     * @param string[] $supportedTypes
     */
    public static function unsupportedType(string $collectorType, array $supportedTypes, ?ContainerExceptionInterface $previous): self
    {
        return new self(
            sprintf('Could not find a collector for type "%s". Supported types: "%s".', $collectorType, implode('", "', $supportedTypes)),
            0,
            $previous
        );
    }
}
