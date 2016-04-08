<?php

namespace SensioLabs\Deptrac\Tests\DependencyEmitter;

use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\AstRunner\AstRunner;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EmitterTrait
{
    public function getDeps(DependencyEmitterInterface $emitter, \SplFileInfo $fileInfo)
    {
        $parser = new NikicPhpParser();
        $astMap = (new AstRunner())->createAstMapByFiles(
            $parser,
            new EventDispatcher(),
            [$fileInfo]
        );
        $result = new DependencyResult();

        $emitter->applyDependencies($parser, $astMap, $result);

        return array_map(function (Dependency $d) {
            return $d->getClassA().':'.$d->getClassALine().' on '.$d->getClassB();
        }, $result->getDependenciesAndInheritDependencies());
    }
}
