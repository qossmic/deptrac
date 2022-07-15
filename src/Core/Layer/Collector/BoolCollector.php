<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use InvalidArgumentException;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;

final class BoolCollector implements ConditionalCollectorInterface
{
    private CollectorResolverInterface $collectorResolver;

    public function __construct(CollectorResolverInterface $collectorResolver)
    {
        $this->collectorResolver = $collectorResolver;
    }

    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        $configuration = $this->normalizeConfiguration($config);

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);

            $satisfied = $collectable->getCollector()->satisfy($collectable->getAttributes(), $reference, $astMap);
            if (!$satisfied) {
                return false;
            }
        }

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must_not'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);

            $satisfied = $collectable->getCollector()->satisfy($collectable->getAttributes(), $reference, $astMap);
            if ($satisfied) {
                return false;
            }
        }

        return true;
    }

    public function resolvable(array $config): bool
    {
        $configuration = $this->normalizeConfiguration($config);

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);
            $collector = $collectable->getCollector();

            if ($collector instanceof ConditionalCollectorInterface
                && !$collector->resolvable($collectable->getAttributes())
            ) {
                return false;
            }
        }

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must_not'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);
            $collector = $collectable->getCollector();

            if ($collector instanceof ConditionalCollectorInterface
                && !$collector->resolvable($collectable->getAttributes())
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string, bool|string|array<string, string>> $configuration
     *
     * @return array<string, bool|string|array<string, string>>
     */
    private function normalizeConfiguration(array $configuration): array
    {
        if (!isset($configuration['must'])) {
            $configuration['must'] = [];
        }

        if (!isset($configuration['must_not'])) {
            $configuration['must_not'] = [];
        }

        if (!$configuration['must'] && !$configuration['must_not']) {
            throw new InvalidArgumentException('"bool" collector must have a "must" or a "must_not" attribute.');
        }

        return $configuration;
    }
}
