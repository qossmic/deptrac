<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency\Emitter;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use Qossmic\Deptrac\Contract\Dependency\DependencyInterface;
use Qossmic\Deptrac\Core\Ast\AstLoader;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\AnonymousClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\FunctionCallExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\FunctionLikeExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\InstanceofExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\NewExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\PropertyExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\StaticCallExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\StaticPropertyFetchExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\TraitUseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\UseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\VariableExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicTypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanContainerDecorator;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanTypeResolver;
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

        $nikicTypeResolver = new NikicTypeResolver();
        $phpstanTypeResolver = new PhpStanTypeResolver();
        $phpStanConstructorDecorator = new PhpStanContainerDecorator('', []);
        $parser = new NikicPhpParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
            new AstFileReferenceInMemoryCache(),
            [
                new AnonymousClassExtractor(),
                new FunctionLikeExtractor($nikicTypeResolver, $phpstanTypeResolver),
                new PropertyExtractor($phpStanConstructorDecorator, $nikicTypeResolver),
                new FunctionCallExtractor($nikicTypeResolver, $phpstanTypeResolver),
                new VariableExtractor($phpStanConstructorDecorator, $nikicTypeResolver),
                new ClassExtractor(),
                new UseExtractor(),
                new InstanceofExtractor($nikicTypeResolver),
                new StaticCallExtractor($nikicTypeResolver),
                new StaticPropertyFetchExtractor($nikicTypeResolver),
                new NewExtractor($nikicTypeResolver),
                new TraitUseExtractor($nikicTypeResolver),
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
