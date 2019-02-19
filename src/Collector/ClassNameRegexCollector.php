<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;

class ClassNameRegexCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'classNameRegex';
    }

    public function satisfy(
        array $configuration,
        AstClassReference $astClassReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool {
        return 1 === preg_match($this->getPattern($configuration), $astClassReference->getClassName());
    }

    private function getPattern(array $configuration): string
    {
        if (!isset($configuration['regex'])) {
            throw new \LogicException('ClassNameRegexCollector needs the regex configuration.');
        }

        return (string) $configuration['regex'];
    }
}
