<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\AstRunner\AstParser\BetterReflection\Parser;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use SensioLabs\Deptrac\Dependency\DependencyInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EmitterTrait
{
    public function getDeps(DependencyEmitterInterface $emitter, \SplFileInfo $fileInfo): array
    {
        $parser = new Parser(new TypeResolver());
        $astMap = (new AstRunner(new EventDispatcher(), $parser))->createAstMapByFiles([$fileInfo]);
        $result = new Result();

        $emitter->applyDependencies($astMap, $result);

        return array_map(
            static function (DependencyInterface $d) {
                return sprintf('%s:%d on %s',
                    $d->getClassLikeNameA()->toString(),
                    $d->getFileOccurrence()->getLine(),
                    $d->getClassLikeNameB()->toString()
                );
            },
            $result->getDependenciesAndInheritDependencies()
        );
    }
}
