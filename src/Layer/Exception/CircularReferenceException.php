<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Exception;

use Qossmic\Deptrac\Utils\ExceptionInterface;
use RuntimeException;
use function implode;
use function sprintf;

final class CircularReferenceException extends RuntimeException implements ExceptionInterface
{
    public static function circularTokenReference(string $tokenName): self
    {
        return new self(sprintf('Circular dependency between layers detected. Token "%s" could not be resolved.', $tokenName));
    }

    /**
     * @param string[] $others
     */
    public static function circularLayerDependency(string $layer, array $others): self
    {
        return new self(sprintf('Circular ruleset dependency for layer %s depending on: %s', $layer, implode('->', $others)));
    }
}
