<?php

namespace DependencyTracker\Collector;

use DependencyTracker\CollectorFactory;
use DependencyTracker\Configuration\ConfigurationCollector;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class BoolCollector implements CollectorInterface
{

    public function getType()
    {
        return 'bool';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        CollectorFactory $collectorFactory
    )
    {
        if (!isset($configuration['must'])) {
            $configuration['must'] = [];
        }

        if (!isset($configuration['must_not'])) {
            $configuration['must_not'] = [];
        }

        if (!$configuration['must'] && !$configuration['must_not']) {
            throw new \InvalidArgumentException("bool collector must have a must or a must_not attribute");
        }

        foreach ($configuration['must'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if (!$collectorFactory->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $abstractClassReference,
                $collectorFactory
            )) {
                return false;
            }
        }

        foreach ($configuration['must_not'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if ($collectorFactory->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $abstractClassReference,
                $collectorFactory
            )) {
                return false;
            }
        }

        return true;
    }

}
