<?php

namespace Qossmic\Deptrac\Contract\Config\Collector;

use Qossmic\Deptrac\Contract\Config\CollectorType;
use Qossmic\Deptrac\Contract\Config\ConfigurableCollectorConfig;

final class ClassConfig extends ConfigurableCollectorConfig
{
    public static function public(string $config): static
    {
        return new self(config: $config, collectorType: CollectorType::TYPE_CLASS, private: false);
    }

    public static function private(string $config): static
    {
        return new self(config: $config, collectorType: CollectorType::TYPE_CLASS, private: true);
    }
}
