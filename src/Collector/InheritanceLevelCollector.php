<?php

namespace DependencyTracker\Collector;

use DependencyTracker\CollectorFactory;
use DependencyTracker\DependencyResult;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class InheritanceLevelCollector implements CollectorInterface
{

    public function getType()
    {
        return 'inheritanceLevel';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory
    ) {
        $classInherits = $astMap->getClassInherits($abstractClassReference->getClassName());

        foreach ($classInherits as $classInherit) {
            if (count($classInherit->getPath()) >= $configuration['level']) {
                return true;
            }
        }

        return false;
    }
}
