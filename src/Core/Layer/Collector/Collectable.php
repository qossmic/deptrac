<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Layer\CollectorInterface;

class Collectable
{
    /**
     * @param array<string, string|array<string, string>> $attributes
     */
    public function __construct(private readonly CollectorInterface $collector, private readonly array $attributes)
    {
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
