<?php

declare(strict_types=1);

use PhpParser\Parser;
use Qossmic\Deptrac\Analyser;
use Qossmic\Deptrac\AstRunner\AstParser\AstFileReferenceCache;
use Qossmic\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\AstRunner\Resolver\AnnotationDependencyResolver;
use Qossmic\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use Qossmic\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\Collector\CollectorInterface;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Configuration\Dumper;
use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Configuration\Loader\YmlFileLoader;
use Qossmic\Deptrac\Configuration\ParameterResolver;
use Qossmic\Deptrac\Dependency\InheritanceFlatter;
use Qossmic\Deptrac\Dependency\Resolver;
use Qossmic\Deptrac\DependencyEmitter\ClassDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\ClassSuperglobalDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\FileDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\FunctionDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\FunctionSuperglobalDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\UsesDependencyEmitter;
use Qossmic\Deptrac\FileResolver;
use Qossmic\Deptrac\LayerAnalyser;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInterface;
use Qossmic\Deptrac\OutputFormatterFactory;
use Qossmic\Deptrac\RulesetEngine;
use Qossmic\Deptrac\TokenAnalyser;
use Qossmic\Deptrac\TokenLayerResolverFactory;
use Qossmic\Deptrac\UnassignedAnalyser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Path;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set(EventDispatcher::class);
    $services->alias(EventDispatcherInterface::class, EventDispatcher::class);

    $services->set(AstRunner::class)
        ->args([
            service(EventDispatcher::class),
            service(NikicPhpParser::class),
        ]);

    $services->set(AstFileReferenceInMemoryCache::class);
    $services->alias(AstFileReferenceCache::class, AstFileReferenceInMemoryCache::class);

    $services
        ->set(Parser::class)
        ->factory([ParserFactory::class, 'createParser']);

    $services
        ->set(NikicPhpParser::class)
        ->args([
            service(Parser::class),
            service(AstFileReferenceCache::class),
            service(TypeResolver::class),
            service(AnnotationDependencyResolver::class),
            service(AnonymousClassResolver::class),
            service(ClassConstantResolver::class),
        ]);

    $services
        ->set(AnnotationDependencyResolver::class)
        ->args([service(TypeResolver::class)]);
    $services->set(AnonymousClassResolver::class);
    $services->set(ClassConstantResolver::class);
    $services->set(TypeResolver::class);

    $services
        ->set(TokenLayerResolverFactory::class)
        ->args([
            service(Registry::class),
            service(ParameterResolver::class),
        ]);

    $services
        ->set(Analyser::class)
        ->args([
            service(AstRunner::class),
            service(FileResolver::class),
            service(Resolver::class),
            service(RulesetEngine::class),
            service(TokenLayerResolverFactory::class),
        ]);

    $services
        ->set(TokenAnalyser::class)
        ->args([
            service(AstRunner::class),
            service(FileResolver::class),
            service(TokenLayerResolverFactory::class),
        ]);

    $services
        ->set(LayerAnalyser::class)
        ->args([
            service(AstRunner::class),
            service(FileResolver::class),
            service(TokenLayerResolverFactory::class),
        ]);

    $services
        ->set(UnassignedAnalyser::class)
        ->args([
            service(AstRunner::class),
            service(FileResolver::class),
            service(TokenLayerResolverFactory::class),
        ]);

    $services->set(RulesetEngine::class);
    $services->set(FileResolver::class);

    /* Configuration */
    $services->set(YmlFileLoader::class);
    $services->set(Dumper::class);
    $services
        ->set(Loader::class)
        ->args([
            service(YmlFileLoader::class),
            param('currentWorkingDirectory'),
        ]);
    $services->set(ParameterResolver::class);

    /* Formatters */
    $services->instanceof(OutputFormatterInterface::class)
        ->tag('output_formatter');

    $services->load('Qossmic\\Deptrac\\OutputFormatter\\', Path::getDirectory(__DIR__).'/src/OutputFormatter')
        ->exclude([
            Path::getDirectory(__DIR__).'/src/OutputFormatter/OutputFormatterInput.php',
        ])
        ->autowire();

    $services
        ->set(OutputFormatterFactory::class)
        ->args([tagged_iterator('output_formatter')]);

    /* Collectors */
    $services->instanceof(CollectorInterface::class)
        ->tag('collector');

    $services->load('Qossmic\\Deptrac\\Collector\\', Path::getDirectory(__DIR__).'/src/Collector')
        ->exclude([
            Path::getDirectory(__DIR__).'/src/Collector/Registry.php',
        ])
        ->autowire();

    $services
        ->set(Registry::class)
        ->args([tagged_iterator('collector')]);

    /* Dependency resolving */
    $services
        ->set(Resolver::class)
        ->args([
            service(EventDispatcher::class),
            service(InheritanceFlatter::class),
            service(ClassDependencyEmitter::class),
            service(ClassSuperglobalDependencyEmitter::class),
            service(FileDependencyEmitter::class),
            service(FunctionDependencyEmitter::class),
            service(FunctionSuperglobalDependencyEmitter::class),
            service(UsesDependencyEmitter::class),
        ]);
    $services->set(InheritanceFlatter::class);
    $services->set(ClassDependencyEmitter::class);
    $services->set(ClassSuperglobalDependencyEmitter::class);
    $services->set(FileDependencyEmitter::class);
    $services->set(FunctionDependencyEmitter::class);
    $services->set(FunctionSuperglobalDependencyEmitter::class);
    $services->set(UsesDependencyEmitter::class);

    /* Commands */
    $services->load('Qossmic\\Deptrac\\Console\\Command\\', Path::getDirectory(__DIR__).'/src/Console/Command')
        ->exclude([
            Path::getDirectory(__DIR__).'/src/Console/Command/*Options.php',
        ])
        ->autowire();
};
