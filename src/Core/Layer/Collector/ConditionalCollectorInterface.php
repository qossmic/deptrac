<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;

interface ConditionalCollectorInterface extends CollectorInterface
{
    /**
     * @param array<string, bool|string|array<string, string>> $config
     *
     * @throws InvalidLayerDefinitionException
     */
    public function resolvable(array $config): bool;
}
