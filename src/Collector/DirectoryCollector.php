<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;

class DirectoryCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'directory';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        AstParserInterface $astParser
    ): bool {
        $fileReference = $abstractClassReference->getFileReference();

        if (null === $fileReference) {
            return false;
        }

        return 1 === preg_match(
            '#'.$this->getRegexByConfiguration($configuration).'#i',
            $fileReference->getFilepath()
        );
    }

    private function getRegexByConfiguration(array $configuration)
    {
        if (!isset($configuration['regex'])) {
            throw new \LogicException('DirectoryCollector needs the regex configuration.');
        }

        return $configuration['regex'];
    }
}
