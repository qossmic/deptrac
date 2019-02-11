<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyResult\Dependency;

class BasicDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'BasicDependencyEmitter';
    }

    public function supportsParser(AstParserInterface $astParser): bool
    {
        return true;
    }

    public function applyDependencies(
        AstParserInterface $astParser,
        AstMap $astMap,
        Result $dependencyResult
    ): void {
        foreach ($astMap->getAstFileReferences() as $fileReference) {
            $uses = $fileReference->getEmittedDependencies();

            foreach ($fileReference->getAstClassReferences() as $astClassReference) {
                /** @var EmittedDependency[] $dependencies */
                $dependencies = array_merge($uses, $astClassReference->getEmittedDependencies());

                foreach ($dependencies as $emittedDependency) {
                    $dependencyResult->addDependency(
                        new Dependency(
                            $astClassReference->getClassName(),
                            $emittedDependency->getLine(),
                            $emittedDependency->getClass()
                        )
                    );
                }
            }
        }
    }
}
