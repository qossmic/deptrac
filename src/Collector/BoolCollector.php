<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\CollectorFactory;
use SensioLabs\Deptrac\Configuration\ConfigurationCollector;
use SensioLabs\AstRunner\AstMap;
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
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    ) {
        if (!isset($configuration['must'])) {
            $configuration['must'] = [];
        }

        if (!isset($configuration['must_not'])) {
            $configuration['must_not'] = [];
        }

        if (!$configuration['must'] && !$configuration['must_not']) {
            throw new \InvalidArgumentException('bool collector must have a must or a must_not attribute');
        }

        foreach ($configuration['must'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if (!$collectorFactory->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $abstractClassReference,
                $astMap,
                $collectorFactory,
                $astParser
            )) {
                return false;
            }
        }

        foreach ($configuration['must_not'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if ($collectorFactory->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $abstractClassReference,
                $astMap,
                $collectorFactory,
                $astParser
            )) {
                return false;
            }
        }

        return true;
    }
}
