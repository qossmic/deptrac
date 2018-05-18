<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;

class ClassNameCollector implements CollectorInterface
{
    public function getType(): string
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
        AstMap $astMap,
        Registry $collectorRegistry,
        AstParserInterface $astParser
    ): bool {
        return 1 === preg_match(
            '/'.$this->getRegexByConfiguration($configuration).'/i',
            $abstractClassReference->getClassName()
        );
    }
}
