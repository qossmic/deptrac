<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;

final class BoolCollector implements ConditionalCollectorInterface
{
    public function __construct(private readonly CollectorResolverInterface $collectorResolver) {}

    public function satisfy(array $config, TokenReferenceInterface $reference): bool
    {
        $configuration = $this->normalizeConfiguration($config);

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);

            $satisfied = $collectable->collector->satisfy($collectable->attributes, $reference);
            if (!$satisfied) {
                return false;
            }
        }

        $satisfied = ([] === $configuration['must_any']);

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must_any'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);

            $satisfied = $collectable->collector->satisfy($collectable->attributes, $reference);
            if ($satisfied) {
                break;
            }
        }

        if (!$satisfied) {
            return false;
        }

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must_not'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);

            $satisfied = $collectable->collector->satisfy($collectable->attributes, $reference);
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
            $collector = $collectable->collector;

            if ($collector instanceof ConditionalCollectorInterface
                && !$collector->resolvable($collectable->attributes)
            ) {
                return false;
            }
        }

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must_any'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);
            $collector = $collectable->collector;

            if ($collector instanceof ConditionalCollectorInterface
                && !$collector->resolvable($collectable->attributes)
            ) {
                return false;
            }
        }

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must_not'] as $v) {
            $collectable = $this->collectorResolver->resolve($v);
            $collector = $collectable->collector;

            if ($collector instanceof ConditionalCollectorInterface
                && !$collector->resolvable($collectable->attributes)
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
     *
     * @throws InvalidCollectorDefinitionException
     */
    private function normalizeConfiguration(array $configuration): array
    {
        if (!isset($configuration['must'])) {
            $configuration['must'] = [];
        }

        if (!isset($configuration['must_any'])) {
            $configuration['must_any'] = [];
        }

        if (!isset($configuration['must_not'])) {
            $configuration['must_not'] = [];
        }

        if (!$configuration['must'] && !$configuration['must_not'] && !$configuration['must_any']) {
            throw InvalidCollectorDefinitionException::invalidCollectorConfiguration('"bool" collector must have at least one of "must", "must_any", or "must_not" attribute.');
        }

        return $configuration;
    }
}
