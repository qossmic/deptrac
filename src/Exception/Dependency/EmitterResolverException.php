<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Exception\Dependency;

use Qossmic\Deptrac\Exception\ExceptionInterface;
use RuntimeException;
use Throwable;
use function sprintf;

final class EmitterResolverException extends RuntimeException implements ExceptionInterface
{
    public static function missingServiceForType(string $type, ?Throwable $previous): self
    {
        return new self(
            sprintf('No emitter is registered for the provided type "%s".', $type),
            0,
            $previous
        );
    }
}
