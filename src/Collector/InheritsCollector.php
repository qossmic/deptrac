<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;

class InheritsCollector implements CollectorInterface
{
    public function resolvable(array $configuration, Registry $collectorRegistry, array $resolutionTable): bool
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
        array $resolutionTable = []
    ): bool {
        if (!$astTokenReference instanceof AstClassReference) {
            return false;
        }

        $classLikeName = $this->getClassLikeName($configuration);

        foreach ($astMap->getClassInherits($astTokenReference->getTokenName()) as $inherit) {
            if ($inherit->getClassLikeName()->equals($classLikeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function getClassLikeName(array $configuration): ClassLikeName
    {
        if (isset($configuration['inherits']) && !isset($configuration['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'InheritsCollector should use the "value" key from this version');
            $configuration['value'] = $configuration['inherits'];
        }

        if (!isset($configuration['value']) || !is_string($configuration['value'])) {
            throw new LogicException('InheritsCollector needs the interface, trait or class name as a string.');
        }

        return ClassLikeName::fromFQCN($configuration['value']);
    }
}
