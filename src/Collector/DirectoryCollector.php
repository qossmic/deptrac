<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\AstFileReference;
use SensioLabs\Deptrac\CollectorFactory;

class DirectoryCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'directory';
    }

    private function getRegexByConfiguration(array $configuration)
    {
        if (!isset($configuration['regex'])) {
            throw new \LogicException('DirectoryCollector needs the regex configuration.');
        }

        return $configuration['regex'];
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    ): bool {
        $fileReference = $abstractClassReference->getFileReference();
        assert($fileReference instanceof AstFileReference);

        return preg_match(
            '#'.$this->getRegexByConfiguration($configuration).'#i',
            $fileReference->getFilepath(),
            $collectorFactory
        );
    }
}
