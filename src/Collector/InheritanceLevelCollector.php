<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\AstClassReference;

class InheritanceLevelCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'inheritanceLevel';
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

        $classInherits = $astMap->getClassInherits($astTokenReference->getTokenName());

        foreach ($classInherits as $classInherit) {
            if (count($classInherit->getPath()) >= $configuration['level']) {
                return true;
            }
        }

        return false;
    }
}
