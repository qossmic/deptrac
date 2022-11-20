<?php

namespace Qossmic\Deptrac\Contract\Config\Collector;

use Qossmic\Deptrac\Contract\Config\CollectorType;
use Qossmic\Deptrac\Contract\Config\ConfigurableCollectorConfig;

final class FunctionNameConfig extends ConfigurableCollectorConfig
{
    public static function public(string $config): static
    {
        return new self(config: $config, collectorType: CollectorType::TYPE_FUNCTION_NAME, private: false);
    }

    public static function private(string $config): static
    {
        return new self(config: $config, collectorType: CollectorType::TYPE_FUNCTION_NAME, private: true);
    }
}
