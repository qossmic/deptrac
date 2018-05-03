<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;

class InheritanceLevelCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'inheritanceLevel';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        AstParserInterface $astParser
    ): bool {
        $classInherits = $astMap->getClassInherits($abstractClassReference->getClassName());

        foreach ($classInherits as $classInherit) {
            if (count($classInherit->getPath()) >= $configuration['level']) {
                return true;
            }
        }

        return false;
    }
}
