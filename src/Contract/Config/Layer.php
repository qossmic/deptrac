<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

final class Layer
{
    /** @var array<CollectorConfig> */
    private array $collectors = [];

    private function __construct(
        public readonly string $name
    ) {
    }

    public static function withName(string $name): self
    {
        return new self($name);
    }

    public function collectors(CollectorConfig ...$collectorConfigs): self
    {
        foreach ($collectorConfigs as $collectorConfig) {
            $this->collectors[] = $collectorConfig;
        }

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'collectors' => array_map(static fn (CollectorConfig $config) => $config->toArray(), $this->collectors),
        ];
    }
}
