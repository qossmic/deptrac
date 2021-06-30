<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use InvalidArgumentException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\Configuration\ConfigurationCollector;

class BoolCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'bool';
    }

    public function satisfy(
        array $configuration,
        AstClassReference $astClassReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool {
        if (!isset($configuration['must'])) {
            $configuration['must'] = [];
        }

        if (!isset($configuration['must_not'])) {
            $configuration['must_not'] = [];
        }

        if (!$configuration['must'] && !$configuration['must_not']) {
            throw new InvalidArgumentException('"bool" collector must have a "must" or a "must_not" attribute.');
        }

        foreach ((array) $configuration['must'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if (!$collectorRegistry->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $astClassReference,
                $astMap,
                $collectorRegistry
            )) {
                return false;
            }
        }

        foreach ((array) $configuration['must_not'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if ($collectorRegistry->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $astClassReference,
                $astMap,
                $collectorRegistry
            )) {
                return false;
            }
        }

        return true;
    }
}
