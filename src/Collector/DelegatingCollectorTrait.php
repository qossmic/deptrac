<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

trait DelegatingCollectorTrait
{
    private $registry;

    public function setRegistry(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @throws \RuntimeException
     */
    public function getRegistry(): Registry
    {
        if(null === $this->registry) {
            throw new \RuntimeException('CollectorRegistry is not registered. Please implement the "DelegatingCollectorInterface"');
        }

        return $this->registry;
    }
}
