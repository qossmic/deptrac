<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Psr\Container\ContainerExceptionInterface;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidCollectorDefinitionException;

use function array_key_exists;
use function is_string;

final class CollectorResolver implements CollectorResolverInterface
{
    public function __construct(private readonly CollectorProvider $collectorProvider)
    {
    }

    /**
     * @param array<string, string|array<string, string>> $config
     */
    public function resolve(array $config): Collectable
    {
        if (!array_key_exists('type', $config) || !is_string($config['type'])) {
            throw InvalidCollectorDefinitionException::missingType();
        }

        try {
            $collector = $this->collectorProvider->get($config['type']);
        } catch (ContainerExceptionInterface $containerException) {
            throw InvalidCollectorDefinitionException::unsupportedType($config['type'], $this->collectorProvider->getKnownCollectors(), $containerException);
        }

        return new Collectable($collector, $config);
    }
}
