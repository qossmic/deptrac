<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;

class ClassNameCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'className';
    }

    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool {
        if (!$astTokenReference instanceof AstClassReference) {
            return false;
        }

        return $astTokenReference->getTokenName()
            ->match($this->getPattern($configuration));
    }

    /**
     * @param array<string, string|array> $configuration
     */
    private function getPattern(array $configuration): string
    {
        if (!isset($configuration['regex']) || !is_string($configuration['regex'])) {
            throw new LogicException('ClassNameCollector needs the regex configuration.');
        }

        return '/'.$configuration['regex'].'/i';
    }
}
