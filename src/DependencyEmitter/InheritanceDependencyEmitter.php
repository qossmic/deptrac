<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\FlattenAstInherit;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class InheritanceDependencyEmitter implements DependencyEmitterInterface
{
    public function getName()
    {
        return 'InheritanceDependencyEmitter';
    }

    public function supportsParser(AstParserInterface $astParser)
    {
        return $astParser instanceof NikicPhpParser;
    }

    public function applyDependencies(
        AstParserInterface $astParser,
        AstMap $astMap,
        DependencyResult $dependencyResult
    ) {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            foreach ($astMap->getClassInherits($classReference->getClassName()) as $inherit) {
                if ($inherit instanceof FlattenAstInherit) {
                    continue;
                }

                $dependencyResult->addDependency(
                    new Dependency(
                        $classReference->getClassName(),
                        $inherit->getLine(),
                        $inherit->getClassName(),
                        Dependency::TYPE_EXTENDS
                    )
                );
            }
        }
    }
}
