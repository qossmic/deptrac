<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\CollectorFactory;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class ClassNameCollector implements CollectorInterface
{
    public function getType()
    {
        return 'className';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    ) {
        return preg_match(
            $this->getConfigurationRegex($configuration),
            $abstractClassReference->getClassName(),
            $collectorFactory
        );
    }

    private function getConfigurationRegex(array $configuration)
    {
        if (!isset($configuration['regex'])) {
            throw new \LogicException('ClassNameCollector needs the regex configuration.');
        }

        return sprintf('/%s/i', $configuration['regex']);
    }
}
