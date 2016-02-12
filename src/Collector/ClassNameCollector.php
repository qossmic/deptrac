<?php

namespace DependencyTracker\Collector;

use DependencyTracker\CollectorFactory;
use DependencyTracker\DependencyResult;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class ClassNameCollector implements CollectorInterface
{

    public function getType()
    {
        return 'className';
    }

    private function getRegexByConfiguration(array $configuration)
    {
        if (!isset($configuration['regex'])) {
            throw new \LogicException('ClassNameCollector needs the regex configuration.');
        }

        return $configuration['regex'];
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        CollectorFactory $collectorFactory
    ) {
        return preg_match(
            '/' . $this->getRegexByConfiguration($configuration) . '/i',
            $abstractClassReference->getClassName(),
            $collectorFactory
        );
    }

}
