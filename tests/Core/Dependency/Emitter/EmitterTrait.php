<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency\Emitter;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use Qossmic\Deptrac\Core\Ast\AstLoader;
use Qossmic\Deptrac\Core\Ast\Parser\AnonymousClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;
use Qossmic\Deptrac\Core\Dependency\DependencyInterface;
use Qossmic\Deptrac\Core\Dependency\DependencyList;
use Qossmic\Deptrac\Core\Dependency\Emitter\DependencyEmitterInterface;
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
                    $d->getFileOccurrence()->line,
                    $d->getDependent()->toString()
                );
            },
            $result->getDependenciesAndInheritDependencies()
        );
    }
}
