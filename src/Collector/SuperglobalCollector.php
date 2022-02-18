<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;

class SuperglobalCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'superglobal';
    }

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
        if (!$astTokenReference instanceof AstMap\AstVariableReference) {
            return false;
        }

        return in_array($astTokenReference->getTokenName()->toString(), $this->getNames($configuration), true);
    }

    /**
     * @param array<string, string|array<string, string>> $configuration
     *
     * @return string[]
     */
    private function getNames(array $configuration): array
    {
        if (isset($configuration['names']) && !isset($configuration['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'SuperglobalCollector should use the "value" key from this version');
            $configuration['value'] = $configuration['names'];
        }

        if (!isset($configuration['value']) || !is_array($configuration['value'])) {
            throw new LogicException('SuperglobalCollector needs the names configuration.');
        }

        return array_map(static fn ($name): string => '$'.(string) $name, $configuration['value']);
    }
}
