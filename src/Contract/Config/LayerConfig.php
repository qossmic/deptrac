<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

use Stringable;

final class LayerConfig implements Stringable
{
    /** @var array<CollectorConfig> */
    private array $colloctors = [];

    public function __construct(
        public readonly string $name
    ) {
    }

    public function collector(CollectorType $collectorType): CollectorConfig
    {
        return $this->colloctors[] = CollectorConfig::fromType($collectorType);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return ['name' => $this->name, 'collectors' => array_map(static fn (CollectorConfig $config) => $config->toArray(), $this->colloctors)];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
