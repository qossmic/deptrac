<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

final class Layer
{
    /** @var array<CollectorConfig> */
    private array $collectors = [];
    /** @var list<string> */
    private array $allowedDependencies;
    public string $name;

    /**
     * @param array<CollectorConfig> $collectorConfig
     * @param list<string> $allowedDependencies
     */
    public function __construct(string $name, array $collectorConfig = [], array $allowedDependencies = [])
    {
        $this->name = $name;
        $this->allowedDependencies($allowedDependencies);
        $this->collectors(...$collectorConfig);
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

    /**
     * @param list<string> $allowedDependencies
     */
    public function allowedDependencies(array $allowedDependencies): self
    {
        $this->allowedDependencies = $allowedDependencies;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'collectors' => array_map(static fn (CollectorConfig $config) => $config->toArray(), $this->collectors),
            'allowed_dependencies' => $this->allowedDependencies,
        ];
    }
}
