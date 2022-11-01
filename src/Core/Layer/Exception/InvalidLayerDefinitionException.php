<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Exception;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;

use function sprintf;

final class InvalidLayerDefinitionException extends RuntimeException implements ExceptionInterface
{
    public static function missingName(): self
    {
        return new self('Could not resolve layer definition. The field "name" is required for all layers.');
    }

    public static function duplicateName(string $layerName): self
    {
        return new self(sprintf('The layer name "%s" is already in use. Names must be unique.', $layerName));
    }

    public static function collectorRequired(string $layerName): self
    {
        return new self(sprintf('The layer "%s" is empty. You must assign at least 1 collector to a layer.', $layerName));
    }

    public static function layerRequired(): self
    {
        return new self('Layer configuration is empty. You need to define at least 1 layer.');
    }
}
