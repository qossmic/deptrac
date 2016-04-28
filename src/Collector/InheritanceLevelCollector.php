<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\CollectorFactory;
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
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
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
