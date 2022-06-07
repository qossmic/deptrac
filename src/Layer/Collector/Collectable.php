<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

class Collectable
{
    private CollectorInterface $collector;

    /**
     * @var array<string, string|array<string, string>>
     */
    private array $attributes;

    /**
     * @param array<string, string|array<string, string>> $attributes
     */
    public function __construct(CollectorInterface $collector, array $attributes)
    {
        $this->collector = $collector;
        $this->attributes = $attributes;
    }

    public function getCollector(): CollectorInterface
    {
        return $this->collector;
    }

    /**
     * @return array<string, bool|string|array<string, string>>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
