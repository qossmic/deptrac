<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

interface ConditionalCollectorInterface extends CollectorInterface
{
    /**
     * @param array<string, bool|string|array<string, string>> $config
     */
    public function resolvable(array $config): bool;
}
