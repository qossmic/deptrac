<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\FlattenAstInherit;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyResult\Dependency;

class InheritanceDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'InheritanceDependencyEmitter';
    }

    public function supportsParser(AstParserInterface $astParser): bool
    {
        return $astParser instanceof NikicPhpParser;
    }

    public function applyDependencies(
        AstParserInterface $astParser,
        AstMap $astMap,
        Result $dependencyResult
    ): void {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            foreach ($astMap->getClassInherits($classReference->getClassName()) as $inherit) {
                if ($inherit instanceof FlattenAstInherit) {
                    continue;
                }

                $dependencyResult->addDependency(
                    new Dependency(
                        $classReference->getClassName(),
                        $inherit->getLine(),
                        $inherit->getClassName()
                    )
                );
            }
        }
    }
}
