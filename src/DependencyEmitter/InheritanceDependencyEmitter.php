<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\FlattenAstInherit;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
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
