<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;

class FunctionNameCollector implements CollectorInterface
{
    public function resolvable(array $configuration, Registry $collectorRegistry, array $resolutionTable): bool
    {
        return true;
    }

    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool {
        if (!$astTokenReference instanceof AstMap\AstFunctionReference) {
            return false;
        }

        /** @var \Qossmic\Deptrac\AstRunner\AstMap\FunctionName $tokenName */
        $tokenName = $astTokenReference->getTokenName();

        return $tokenName->match($this->getPattern($configuration));
    }

    /**
     * @param array<string, string|array<string, string>> $configuration
     */
    private function getPattern(array $configuration): string
    {
        if (!isset($configuration['regex']) || !is_string($configuration['regex'])) {
            throw new LogicException('FunctionNameCollector needs the regex configuration.');
        }

        return '/'.$configuration['regex'].'/i';
    }
}
