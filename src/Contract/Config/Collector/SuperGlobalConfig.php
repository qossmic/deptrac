<?php

namespace Qossmic\Deptrac\Contract\Config\Collector;

use Qossmic\Deptrac\Contract\Config\CollectorConfig;
use Qossmic\Deptrac\Contract\Config\CollectorType;

final class SuperGlobalConfig extends CollectorConfig
{
    /**
     * @param string[] $config
     */
    protected function __construct(
        protected array $config,
        protected bool $private = false,
    ) {
    }

    public static function public(string ...$config): static
    {
        return new self(config: $config);
    }

    public static function private(string ...$config): static
    {
        return new self(config: $config, private: true);
    }

    /**
     * @return array{'private': bool, 'type': string, 'value': string[]}
     */
    public function toArray(): array
    {
        return [
            'private' => $this->private,
            'type' => CollectorType::TYPE_SUPERGLOBAL->value,
            'value' => $this->config,
        ];
    }
}
