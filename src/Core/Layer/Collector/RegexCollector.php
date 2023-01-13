<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;

abstract class RegexCollector implements CollectorInterface
{
    /**
     * @param array<string, bool|string|array<string, string>> $config
     *
     * @throws InvalidLayerDefinitionException
     */
    abstract protected function getPattern(array $config): string;

    /**
     * @param array<string, bool|string|array<string, string>> $config
     *
     * @throws InvalidLayerDefinitionException
     */
    protected function getValidatedPattern(array $config): string
    {
        $pattern = $this->getPattern($config);
        if (false !== @preg_match($pattern, '')) {
            return $pattern;
        }
        throw InvalidLayerDefinitionException::invalidCollectorConfiguration('Invalid regex pattern '.$pattern);
    }
}
