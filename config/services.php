<?php

declare(strict_types=1);

use PhpParser\Parser;
use SensioLabs\Deptrac\Analyser;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceCache;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\FileParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use SensioLabs\Deptrac\AstRunner\Resolver\AnnotationDependencyResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use SensioLabs\Deptrac\Collector\BoolCollector;
use SensioLabs\Deptrac\Collector\ClassNameCollector;
use SensioLabs\Deptrac\Collector\ClassNameRegexCollector;
use SensioLabs\Deptrac\Collector\DirectoryCollector;
use SensioLabs\Deptrac\Collector\ImplementsCollector;
use SensioLabs\Deptrac\Collector\InheritanceLevelCollector;
use SensioLabs\Deptrac\Collector\MethodCollector;
use SensioLabs\Deptrac\Collector\Registry;
use SensioLabs\Deptrac\Configuration\Dumper;
use SensioLabs\Deptrac\Configuration\Loader;
use SensioLabs\Deptrac\Console\Command\AnalyzeCommand;
use SensioLabs\Deptrac\Console\Command\InitCommand;
use SensioLabs\Deptrac\Dependency\InheritanceFlatter;
use SensioLabs\Deptrac\Dependency\Resolver;
use SensioLabs\Deptrac\DependencyEmitter\BasicDependencyEmitter;
use SensioLabs\Deptrac\DependencyEmitter\InheritanceDependencyEmitter;
use SensioLabs\Deptrac\FileResolver;
use SensioLabs\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\GraphVizOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\JUnitOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\XMLOutputFormatter;
use SensioLabs\Deptrac\OutputFormatterFactory;
use SensioLabs\Deptrac\RulesetEngine;
use Symfony\Component\DependencyInjection\Loader\Configurator as di;
use Symfony\Component\EventDispatcher\EventDispatcher;

return static function (di\ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->private();

    $services->set(EventDispatcher::class);

    $services->set(AstRunner::class)
        ->args([
            di\ref(EventDispatcher::class),
            di\ref(NikicPhpParser::class),
        ]);

    $services->set(AstFileReferenceInMemoryCache::class);
    $services->alias(AstFileReferenceCache::class, AstFileReferenceInMemoryCache::class);

    $services
        ->set(Parser::class)
        ->factory([ParserFactory::class, 'createParser']);

    $services
        ->set(FileParser::class)
        ->args([di\ref(Parser::class)]);

    $services
        ->set(NikicPhpParser::class)
        ->args([
            di\ref(FileParser::class),
            di\ref(AstFileReferenceCache::class),
            di\ref(TypeResolver::class),
            di\ref(AnnotationDependencyResolver::class),
            di\ref(AnonymousClassResolver::class),
            di\ref(ClassConstantResolver::class),
        ]);

    $services
        ->set(AnnotationDependencyResolver::class)
        ->args([di\ref(TypeResolver::class)]);
    $services->set(AnonymousClassResolver::class);
    $services->set(ClassConstantResolver::class);
    $services->set(TypeResolver::class);

    $services
        ->set(Analyser::class)
        ->args([
            di\ref(AstRunner::class),
            di\ref(FileResolver::class),
            di\ref(Resolver::class),
            di\ref(Registry::class),
            di\ref(RulesetEngine::class),
        ]);

    $services->set(RulesetEngine::class);
    $services->set(FileResolver::class);

    /* Configuration */
    $services->set(Dumper::class);
    $services->set(Loader::class);

    /* Formatters */
    $services
        ->set(OutputFormatterFactory::class)
        ->args([di\tagged_iterator('output_formatter')]);
    $services
        ->set(ConsoleOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(GithubActionsOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(GraphVizOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(JUnitOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(XMLOutputFormatter::class)
        ->tag('output_formatter');

    /* Collectors */
    $services
        ->set(Registry::class)
        ->args([di\tagged_iterator('collector')]);
    $services
        ->set(BoolCollector::class)
        ->tag('collector');
    $services
        ->set(ClassNameCollector::class)
        ->tag('collector');
    $services
        ->set(ClassNameRegexCollector::class)
        ->tag('collector');
    $services
        ->set(DirectoryCollector::class)
        ->tag('collector');
    $services
        ->set(InheritanceLevelCollector::class)
        ->tag('collector');
    $services
        ->set(ImplementsCollector::class)
        ->tag('collector');
    $services
        ->set(MethodCollector::class)
        ->args([di\ref(NikicPhpParser::class)])
        ->tag('collector');

    /* Dependency resolving */
    $services
        ->set(Resolver::class)
        ->args([
            di\ref(EventDispatcher::class),
            di\ref(InheritanceFlatter::class),
            di\tagged_iterator('dependency_emitter'),
        ]);
    $services->set(InheritanceFlatter::class);
    $services
        ->set(InheritanceDependencyEmitter::class)
        ->tag('dependency_emitter');
    $services
        ->set(BasicDependencyEmitter::class)
        ->tag('dependency_emitter');

    /* Commands */
    $services
        ->set(InitCommand::class)
        ->args([di\ref(Dumper::class)])
        ->public();

    $services
        ->set(AnalyzeCommand::class)
        ->args([
            di\ref(Analyser::class),
            di\ref(Loader::class),
            di\ref(EventDispatcher::class),
            di\ref(OutputFormatterFactory::class),
        ])
        ->public();
};
