<?php

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\AstRunner\AstRunner;
use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EmitterTrait
{
    public function getDeps(DependencyEmitterInterface $emitter, \SplFileInfo $fileInfo): array
    {
        $parser = new NikicPhpParser();
        $astMap = (new AstRunner(new EventDispatcher()))->createAstMapByFiles(
            $parser,
            [$fileInfo]
        );
        $result = new DependencyResult();

        $emitter->applyDependencies($parser, $astMap, $result);

        return array_map(
            function (Dependency $d) {
                return $d->getClassA().':'.$d->getClassALine().' on '.$d->getClassB();
            },
            $result->getDependenciesAndInheritDependencies()
        );
    }
}
