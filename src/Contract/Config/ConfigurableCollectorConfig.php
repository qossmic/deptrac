<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

abstract class ConfigurableCollectorConfig extends CollectorConfig
{
    final protected function __construct(
        protected string $config,
        protected CollectorType $collectorType = CollectorType::TYPE_BOOL,
        protected bool $private = false,
    ) {
    }

    public static function public(string $config): static
    {
        return new static($config);
    }

    public static function private(string $config): static
    {
        return new static(config: $config, private: true);
    }

    /**
     * @return array{private: bool, type: string, value: mixed|string}
     */
    public function toArray(): array
    {
        return parent::toArray() + [
            'value' => $this->config,
        ];
    }
}
