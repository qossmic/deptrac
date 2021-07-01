<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use InvalidArgumentException;

class Registry
{
    /** @var CollectorInterface[] */
    protected array $collectors = [];

    /**
     * @param iterable<CollectorInterface> $collectors
     */
    public function __construct(iterable $collectors)
    {
        foreach ($collectors as $collector) {
            $this->addCollector($collector);
        }
    }

    /**
     * @throws InvalidArgumentException if collector does not exists
     */
    public function getCollector(string $type): CollectorInterface
    {
        if (array_key_exists($type, $this->collectors)) {
            return $this->collectors[$type];
        }

        foreach ($this->collectors as $collector) {
            if (get_class($collector) === $type) {
                return $collector;
            }
        }

        if (class_exists($type) && is_subclass_of($type, CollectorInterface::class)) {
            $collector = new $type();
            $this->addCollector($collector);

            return $collector;
        }

        throw new InvalidArgumentException(sprintf('unknown collector type "%s", possible collectors are %s', $type, implode(', ', array_keys($this->collectors))));
    }

    private function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[$collector->getType()] = $collector;
    }
}
