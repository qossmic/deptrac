<?php

declare(strict_types=1);

use PhpParser\Parser;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qossmic\Deptrac\Analyser;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceCacheInterface;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\AstRunner\Resolver\AnnotationDependencyResolver;
use Qossmic\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use Qossmic\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\Collector\BoolCollector;
use Qossmic\Deptrac\Collector\ClassNameCollector;
use Qossmic\Deptrac\Collector\ClassNameRegexCollector;
use Qossmic\Deptrac\Collector\DirectoryCollector;
use Qossmic\Deptrac\Collector\ExtendsCollector;
use Qossmic\Deptrac\Collector\FunctionNameCollector;
use Qossmic\Deptrac\Collector\ImplementsCollector;
use Qossmic\Deptrac\Collector\InheritanceLevelCollector;
use Qossmic\Deptrac\Collector\InheritsCollector;
use Qossmic\Deptrac\Collector\LayerCollector;
use Qossmic\Deptrac\Collector\MethodCollector;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Collector\SuperglobalCollector;
use Qossmic\Deptrac\Collector\UsesCollector;
use Qossmic\Deptrac\Configuration\Dumper;
use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Configuration\Loader\YmlFileLoader;
use Qossmic\Deptrac\Configuration\ParameterResolver;
use Qossmic\Deptrac\Console\Command\AnalyseCommand;
use Qossmic\Deptrac\Console\Command\AnalyseRunner;
use Qossmic\Deptrac\Console\Command\DebugLayerCommand;
use Qossmic\Deptrac\Console\Command\DebugLayerRunner;
use Qossmic\Deptrac\Console\Command\DebugTokenCommand;
use Qossmic\Deptrac\Console\Command\DebugTokenRunner;
use Qossmic\Deptrac\Console\Command\DebugUnassignedCommand;
use Qossmic\Deptrac\Console\Command\DebugUnassignedRunner;
use Qossmic\Deptrac\Console\Command\InitCommand;
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
use Qossmic\Deptrac\OutputFormatter\BaselineOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\CodeclimateOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputDisplayFormatter;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputDotFormatter;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputHtmlFormatter;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputImageFormatter;
use Qossmic\Deptrac\OutputFormatter\JsonOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\JUnitOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\TableOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\XMLOutputFormatter;
use Qossmic\Deptrac\OutputFormatterFactory;
use Qossmic\Deptrac\RulesetEngine;
use Qossmic\Deptrac\TokenAnalyser;
use Qossmic\Deptrac\TokenLayerResolverFactory;
use Qossmic\Deptrac\UnassignedAnalyser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set(EventDispatcher::class);
    $services->alias(EventDispatcherInterface::class, EventDispatcher::class);

    $services->set(AstRunner::class)
        ->args([
            service(EventDispatcherInterface::class),
            service(NikicPhpParser::class),
        ]);

    $services->set(AstFileReferenceInMemoryCache::class);
    $services->alias(AstFileReferenceCacheInterface::class, AstFileReferenceInMemoryCache::class);

    $services
        ->set(Parser::class)
        ->factory([ParserFactory::class, 'createParser']);

    $services
        ->set(NikicPhpParser::class)
        ->args([
            service(Parser::class),
            service(AstFileReferenceCacheInterface::class),
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

    $services
        ->set(RulesetEngine::class)
        ->args([
            service(EventDispatcherInterface::class),
        ]);

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
    $services
        ->set(OutputFormatterFactory::class)
        ->args([tagged_iterator('output_formatter')]);
    $services
        ->set(ConsoleOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(GithubActionsOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(GraphVizOutputDisplayFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(GraphVizOutputImageFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(GraphVizOutputDotFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(GraphVizOutputHtmlFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(JUnitOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(TableOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(XMLOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(BaselineOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(JsonOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(CodeclimateOutputFormatter::class)
        ->tag('output_formatter');

    /* Collectors */
    $services
        ->set(Registry::class)
        ->args([tagged_iterator('collector', 'type')]);
    $services
        ->set(LayerCollector::class)
        ->tag('collector', ['type' => 'layer']);
    $services
        ->set(BoolCollector::class)
        ->tag('collector', ['type' => 'bool']);
    $services
        ->set(ClassNameCollector::class)
        ->tag('collector', ['type' => 'className']);
    $services
        ->set(FunctionNameCollector::class)
        ->tag('collector', ['type' => 'functionName']);
    $services
        ->set(ClassNameRegexCollector::class)
        ->tag('collector', ['type' => 'classNameRegex']);
    $services
        ->set(DirectoryCollector::class)
        ->tag('collector', ['type' => 'directory']);
    $services
        ->set(InheritanceLevelCollector::class)
        ->tag('collector', ['type' => 'inheritanceLevel']);
    $services
        ->set(ImplementsCollector::class)
        ->tag('collector', ['type' => 'implements']);
    $services
        ->set(ExtendsCollector::class)
        ->tag('collector', ['type' => 'extends']);
    $services
        ->set(InheritsCollector::class)
        ->tag('collector', ['type' => 'inherits']);
    $services
        ->set(UsesCollector::class)
        ->tag('collector', ['type' => 'uses']);
    $services
        ->set(MethodCollector::class)
        ->args([service(NikicPhpParser::class)])
        ->tag('collector', ['type' => 'method']);
    $services
        ->set(SuperglobalCollector::class)
        ->tag('collector', ['type' => 'superGlobal']);

    /* Dependency resolving */
    $services
        ->set(Resolver::class)
        ->args([
            service(EventDispatcherInterface::class),
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
    $services
        ->set(InitCommand::class)
        ->args([service(Dumper::class)])
        ->tag('console.command');

    $services
        ->set(AnalyseRunner::class)
        ->autowire();

    $services
        ->set(AnalyseCommand::class)
        ->args([
            service(AnalyseRunner::class),
            service(EventDispatcher::class),
            service(OutputFormatterFactory::class),
        ])
        ->tag('console.command');

    $services
        ->set(DebugLayerRunner::class)
        ->autowire();

    $services
        ->set(DebugLayerCommand::class)
        ->autowire()
        ->tag('console.command');

    $services
        ->set(DebugTokenRunner::class)
        ->autowire();

    $services
        ->set(DebugTokenCommand::class)
        ->autowire()
        ->tag('console.command');

    $services
        ->set(DebugUnassignedRunner::class)
        ->autowire();

    $services
        ->set(DebugUnassignedCommand::class)
        ->autowire()
        ->tag('console.command');
};
