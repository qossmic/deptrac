<?php


namespace DependencyTracker\Tests\DependencyEmitter;


use DependencyTracker\DependencyEmitter\DependencyEmitterInterface;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
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

        return array_map(function(Dependency $d) {
            return $d->getClassA().':'.$d->getClassALine().' on '.$d->getClassB();
        }, $result->getDependencies());
    }

}