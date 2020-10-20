<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration\Exception;

final class InvalidConfigurationException extends \InvalidArgumentException
{
    public static function fromDuplicateLayerNames(string ...$layerNames): self
    {
        natsort($layerNames);

        return new self(sprintf(
            'Configuration can not contain multiple layers with the same name, got "%s" as duplicate.',
            implode('", "', $layerNames)
        ));
    }
}
