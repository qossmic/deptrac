<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;

class DirectoryCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'directory';
    }

    public function satisfy(
        array $configuration,
        AstMap\AstClassReference $astClassReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        AstParserInterface $astParser
    ): bool {
        $fileReference = $astClassReference->getFileReference();

        return $fileReference && 1 === preg_match($this->getPattern($configuration), $fileReference->getFilepath());
    }

    private function getPattern(array $configuration): string
    {
        if (!isset($configuration['regex'])) {
            throw new \LogicException('DirectoryCollector needs the regex configuration.');
        }

        return '#'.$configuration['regex'].'#i';
    }
}
