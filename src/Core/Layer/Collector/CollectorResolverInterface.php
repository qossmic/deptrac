<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Layer\Collector;

interface CollectorResolverInterface
{
    /**
     * @param array<string, string|array<string, string>> $config
     */
    public function resolve(array $config) : \Qossmic\Deptrac\Core\Layer\Collector\Collectable;
}
