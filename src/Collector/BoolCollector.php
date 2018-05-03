<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Configuration\ConfigurationCollector;

class BoolCollector implements CollectorInterface, DelegatingCollectorInterface
{
    use DelegatingCollectorTrait;

    public function getType(): string
    {
        return 'bool';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        AstParserInterface $astParser
    ): bool {
        if (!isset($configuration['must'])) {
            $configuration['must'] = [];
        }

        if (!isset($configuration['must_not'])) {
            $configuration['must_not'] = [];
        }

        if (!$configuration['must'] && !$configuration['must_not']) {
            throw new \InvalidArgumentException('bool collector must have a must or a must_not attribute');
        }

        $collectorRegistry = $this->getRegistry();

        foreach ($configuration['must'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if (!$collectorRegistry->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $abstractClassReference,
                $astMap,
                $astParser
            )) {
                return false;
            }
        }

        foreach ($configuration['must_not'] as $v) {
            $configurationForCollector = ConfigurationCollector::fromArray($v);

            if ($collectorRegistry->getCollector($configurationForCollector->getType())->satisfy(
                $configurationForCollector->getArgs(),
                $abstractClassReference,
                $astMap,
                $astParser
            )) {
                return false;
            }
        }

        return true;
    }
}
