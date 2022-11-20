<?php

namespace Qossmic\Deptrac\Contract\Config\Collector;

use Qossmic\Deptrac\Contract\Config\CollectorType;
use Qossmic\Deptrac\Contract\Config\ConfigurableCollectorConfig;

final class AttributeConfig extends ConfigurableCollectorConfig
{
    public static function public(string $config): self
    {
        return new self($config, CollectorType::TYPE_BOOL, false);
    }

    public static function private(string $config): self
    {
        return new self($config, CollectorType::TYPE_BOOL, true);
    }
}
