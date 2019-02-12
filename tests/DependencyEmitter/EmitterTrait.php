<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\FileParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EmitterTrait
{
    public function getDeps(DependencyEmitterInterface $emitter, \SplFileInfo $fileInfo): array
    {
        $parser = new NikicPhpParser(new FileParser(ParserFactory::createParser()));
        $astMap = (new AstRunner(new EventDispatcher()))->createAstMapByFiles(
            $parser,
            [$fileInfo]
        );
        $result = new Result();

        $emitter->applyDependencies($astMap, $result);

        return array_map(
            function (Dependency $d) {
                return $d->getClassA().':'.$d->getClassALine().' on '.$d->getClassB();
            },
            $result->getDependenciesAndInheritDependencies()
        );
    }
}
