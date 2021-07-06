<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\DependencyEmitter;

use Qossmic\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyzer;
use Qossmic\Deptrac\Dependency\DependencyInterface;
use Qossmic\Deptrac\Dependency\Result;
use Qossmic\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EmitterTrait
{
    public function getDeps(DependencyEmitterInterface $emitter, string $file): array
    {
        $parser = new NikicPhpParser(
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver()
        );
        $astMap = (new AstRunner(new EventDispatcher(), $parser))->createAstMapByFiles([$file], ConfigurationAnalyzer::fromArray(
            []));
        $result = new Result();

        $emitter->applyDependencies($astMap, $result);

        return array_map(
            static function (DependencyInterface $d) {
                return sprintf('%s:%d on %s',
                    $d->getDependant()->toString(),
                    $d->getFileOccurrence()->getLine(),
                    $d->getDependee()->toString()
                );
            },
            $result->getDependenciesAndInheritDependencies()
        );
    }
}
