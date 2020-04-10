<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

class Registry
{
    /** @var CollectorInterface[] */
    protected $collectors = [];

    /**
     * @param CollectorInterface[] $collectors
     */
    public function __construct($collectors)
    {
        foreach ($collectors as $collector) {
            $this->addCollector($collector);
        }
    }

    /**
     * @throws \InvalidArgumentException if collector does not exists
     */
    public function getCollector(string $type): CollectorInterface
    {
        if (!isset($this->collectors[$type])) {
            throw new \InvalidArgumentException(sprintf('unknown collector type "%s", possible collectors are %s', $type, implode(', ', array_keys($this->collectors))));
        }

        return $this->collectors[$type];
    }

    private function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[$collector->getType()] = $collector;
    }
}
