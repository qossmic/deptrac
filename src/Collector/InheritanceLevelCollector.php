<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;

class InheritanceLevelCollector implements CollectorInterface
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
        if (!$astTokenReference instanceof AstClassReference) {
            return false;
        }

        $classInherits = $astMap->getClassInherits($astTokenReference->getTokenName());

        if (isset($configuration['level']) && !isset($configuration['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'InheritanceLevelCollector should use the "value" key from this version');
            $configuration['value'] = $configuration['level'];
        }

        foreach ($classInherits as $classInherit) {
            if (count($classInherit->getPath()) >= $configuration['value']) {
                return true;
            }
        }

        return false;
    }
}
