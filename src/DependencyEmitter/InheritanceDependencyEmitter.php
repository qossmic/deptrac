<?php

namespace DependencyTracker\DependencyEmitter;

use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\FlattenAstInherit;
use SensioLabs\AstRunner\AstParser\AstParserInterface;

class InheritanceDependencyEmitter implements DependencyEmitterInterface
{
    public function getName()
    {
        return 'InheritanceDependencyEmitter';
    }

    public function applyDependencies(
        AstParserInterface $astParser,
        AstMap $astMap,
        DependencyResult $dependencyResult
    ) {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            foreach ($astMap->getClassInherits($classReference->getClassName()) as $inherit) {

                // for now we just care about direct inheritance
                if ($inherit instanceof FlattenAstInherit) {
                    continue;
                }

                $dependencyResult->addDependency(
                    new Dependency(
                        $classReference->getClassName(),
                        $inherit->getLine(),
                        $inherit->getClassName(),
                        0,
                        '?'
                    )
                );
            }
        }
    }

}
