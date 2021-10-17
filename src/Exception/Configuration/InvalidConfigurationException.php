<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Exception\Configuration;

use InvalidArgumentException;
use Qossmic\Deptrac\Exception\ExceptionInterface;

final class InvalidConfigurationException extends InvalidArgumentException implements ExceptionInterface
{
    public static function fromDuplicateLayerNames(string ...$layerNames): self
    {
        natsort($layerNames);

        return new self(sprintf(
            'Configuration can not contain multiple layers with the same name, got "%s" as duplicate.',
            implode('", "', $layerNames)
        ));
    }

    public static function fromUnknownLayerNames(string ...$layerNames): self
    {
        natsort($layerNames);

        return new self(sprintf(
            'Configuration can not reference rule sets with unknown layer names, got "%s" as unknown.',
            implode('", "', $layerNames)
        ));
    }
}
