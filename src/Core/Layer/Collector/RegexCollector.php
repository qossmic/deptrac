<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
abstract class RegexCollector implements CollectorInterface
{
    /**
     * @param array<string, bool|string|array<string, string>> $config
     *
     * @throws InvalidCollectorDefinitionException
     */
    protected abstract function getPattern(array $config) : string;
    /**
     * @param array<string, bool|string|array<string, string>> $config
     *
     * @throws InvalidCollectorDefinitionException
     */
    protected function getValidatedPattern(array $config) : string
    {
        $pattern = $this->getPattern($config);
        if (\false !== @\preg_match($pattern, '')) {
            return $pattern;
        }
        throw InvalidCollectorDefinitionException::invalidCollectorConfiguration('Invalid regex pattern ' . $pattern);
    }
}
