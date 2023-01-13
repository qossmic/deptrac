<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Exception;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;

use function implode;
use function sprintf;

final class CircularReferenceException extends RuntimeException implements ExceptionInterface
{
    /**
     * @param string[] $others
     */
    public static function circularLayerDependency(string $layer, array $others): self
    {
        return new self(sprintf('Circular ruleset dependency for layer %s depending on: %s', $layer, implode('->', $others)));
    }
}
