<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

use LogicException;

abstract class RegexCollector implements CollectorInterface
{
    /**
     * @param array<string, string|array<string, string>> $config
     */
    abstract protected function getPattern(array $config): string;

    public function resolvable(array $config): bool
    {
        return true;
    }

    /**
     * @param array<string, string|array<string, string>> $config
     */
    protected function getValidatedPattern(array $config): string
    {
        $pattern = $this->getPattern($config);
        if (false !== @preg_match($pattern, '')) {
            return $pattern;
        }
        throw new LogicException('Invalid regex pattern '.$pattern);
    }
}
