<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;

class ImplementsCollector implements CollectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'implements';
    }

    public function resolvable(array $configuration, Registry $collectorRegistry, array $alreadyResolvedLayers): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $allLayersConfiguration = []
    ): bool {
        if (!$astTokenReference instanceof AstClassReference) {
            return false;
        }

        $interfaceName = $this->getInterfaceName($configuration);

        foreach ($astMap->getClassInherits($astTokenReference->getTokenName()) as $inherit) {
            if ($inherit->isImplements() && $inherit->getClassLikeName()->equals($interfaceName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function getInterfaceName(array $configuration): ClassLikeName
    {
        if (!isset($configuration['implements']) || !is_string($configuration['implements'])) {
            throw new LogicException('ImplementsCollector needs the interface name as a string.');
        }

        return ClassLikeName::fromFQCN($configuration['implements']);
    }
}
