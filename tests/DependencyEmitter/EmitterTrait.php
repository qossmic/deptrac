<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\FileParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use SensioLabs\Deptrac\Dependency\DependencyInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EmitterTrait
{
    public function getDeps(DependencyEmitterInterface $emitter, \SplFileInfo $fileInfo): array
    {
        $parser = new NikicPhpParser(
            new FileParser(ParserFactory::createParser()),
            new AstFileReferenceInMemoryCache()
        );
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
