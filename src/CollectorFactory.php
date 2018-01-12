<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\Collector\CollectorInterface;

class CollectorFactory
{
    /** @var CollectorInterface[] */
    protected $collectors = [];

    /** @param CollectorInterface[] $collectors */
    public function __construct(array $collectors)
    {
        foreach ($collectors as $collector) {
            $this->collectors[$collector->getType()] = $collector;
        }
    }

    /**
     * @param CollectorInterface $type
     *
     * @return CollectorInterface
     */
    public function getCollector($type)
    {
        if (!isset($this->collectors[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'unknown collector type "%s", possible collectors are %s',
                $type,
                implode(', ', array_keys($this->collectors))
            ));
        }

        return $this->collectors[$type];
    }
}
