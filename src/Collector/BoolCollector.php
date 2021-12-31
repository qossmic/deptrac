<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use InvalidArgumentException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Configuration\ConfigurationCollector;

class BoolCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'bool';
    }

    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool {
        $configuration = $this->normalizeConfiguration($configuration);

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if (!$collectorRegistry->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $astTokenReference,
                $astMap,
                $collectorRegistry,
                $resolutionTable
            )) {
                return false;
            }
        }

        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must_not'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if ($collectorRegistry->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $astTokenReference,
                $astMap,
                $collectorRegistry,
                $resolutionTable
            )) {
                return false;
            }
        }

        return true;
    }

    public function resolvable(array $configuration, Registry $collectorRegistry, array $resolutionTable): bool
    {
        $configuration = $this->normalizeConfiguration($configuration);
        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);
            if (!$collectorRegistry->getCollector($configurationForCollector->getType())->resolvable(
                $configurationForCollector->getArgs(), $collectorRegistry, $resolutionTable
            )) {
                return false;
            }
        }
        /** @var array{type: string, args: array<string, string>} $v */
        foreach ((array) $configuration['must_not'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);
            if (!$collectorRegistry->getCollector($configurationForCollector->getType())->resolvable(
                $configurationForCollector->getArgs(), $collectorRegistry, $resolutionTable
            )) {
                return false;
            }
        }

        return true;
    }

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
