<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

abstract class RegexCollector
{
    /**
     * @param array<string, string|array> $configuration
     */
    abstract protected function getPattern(array $configuration): string;

    public function resolvable(array $configuration, Registry $collectorRegistry, array $alreadyResolvedLayers): bool
    {
        return true;
    }

    /**
     * @param array<string, string|array> $configuration
     */
    protected function getValidatedPattern(array $configuration): string
    {
        $pattern = $this->getPattern($configuration);
        if (false !== @preg_match($pattern, '')) {
            return $pattern;
        }
        throw new \LogicException('Invalid regex pattern '.$pattern);
    }
}
