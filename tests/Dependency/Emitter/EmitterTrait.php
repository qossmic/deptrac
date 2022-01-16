<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\Contracts\Dependency\EmitterInterface;
use Qossmic\Deptrac\Dependency\DependencyInterface;
use Qossmic\Deptrac\Dependency\Result;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EmitterTrait
{
    /**
     * @param string|string[] $files
     */
    public function getDeps(EmitterInterface $emitter, $files): array
    {
        $files = (array) $files;

        $parser = new NikicPhpParser(
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            new AnonymousClassResolver()
        );
        $astMap = (new AstRunner(new EventDispatcher(), $parser))->createAstMapByFiles($files);
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
