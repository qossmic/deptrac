<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;

class ClassNameRegexCollector extends RegexCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'classNameRegex';
    }

    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool {
        if (!$astTokenReference instanceof AstClassReference) {
            return false;
        }

        return $astTokenReference->getTokenName()
            ->match($this->getValidatedPattern($configuration));
    }

    protected function getPattern(array $configuration): string
    {
        if (isset($configuration['regex']) && !isset($configuration['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'ClassNameRegexCollector should use the "value" key from this version');
            $configuration['value'] = $configuration['regex'];
        }

        if (!isset($configuration['value']) || !is_string($configuration['value'])) {
            throw new LogicException('ClassNameRegexCollector needs the regex configuration.');
        }

        return $configuration['value'];
    }
}
