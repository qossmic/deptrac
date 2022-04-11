<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency\Emitter;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use Qossmic\Deptrac\Ast\AstLoader;
use Qossmic\Deptrac\Ast\Parser\AnonymousClassExtractor;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Ast\Parser\TypeResolver;
use Qossmic\Deptrac\Dependency\DependencyInterface;
use Qossmic\Deptrac\Dependency\DependencyList;
use Qossmic\Deptrac\Dependency\Emitter\DependencyEmitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

trait EmitterTrait
{
    /**
     * @param string|string[] $files
     */
    public function getEmittedDependencies(DependencyEmitterInterface $emitter, $files): array
    {
        $files = (array) $files;

        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            [
                new AnonymousClassExtractor(),
            ]
        );
        $astMap = (new AstLoader($parser, new EventDispatcher()))->createAstMap($files);
        $result = new DependencyList();

        $emitter->applyDependencies($astMap, $result);

        return array_map(
            static function (DependencyInterface $d) {
                return sprintf('%s:%d on %s',
                    $d->getDepender()->toString(),
                    $d->getFileOccurrence()->getLine(),
                    $d->getDependent()->toString()
                );
            },
            $result->getDependenciesAndInheritDependencies()
        );
    }
}
