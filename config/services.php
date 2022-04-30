<?php

declare(strict_types=1);

use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qossmic\Deptrac\Analyser\AstMapExtractor;
use Qossmic\Deptrac\Analyser\DependencyLayersAnalyser;
use Qossmic\Deptrac\Analyser\EventHandler\AllowDependencyHandler;
use Qossmic\Deptrac\Analyser\EventHandler\MatchingLayersHandler;
use Qossmic\Deptrac\Analyser\EventHandler\UncoveredDependentHandler;
use Qossmic\Deptrac\Analyser\EventHandler\ViolationHandler;
use Qossmic\Deptrac\Analyser\LayerForTokenAnalyser;
use Qossmic\Deptrac\Analyser\LegacyDependencyLayersAnalyser;
use Qossmic\Deptrac\Analyser\TokenInLayerAnalyser;
use Qossmic\Deptrac\Analyser\UnassignedTokenAnalyser;
use Qossmic\Deptrac\Ast\AstLoader;
use Qossmic\Deptrac\Ast\Parser\AnnotationReferenceExtractor;
use Qossmic\Deptrac\Ast\Parser\AnonymousClassExtractor;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceCacheInterface;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Ast\Parser\ClassConstantExtractor;
use Qossmic\Deptrac\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Ast\Parser\ParserInterface;
use Qossmic\Deptrac\Ast\Parser\TypeResolver;
use Qossmic\Deptrac\Configuration\FormatterConfiguration;
use Qossmic\Deptrac\Console\Command\AnalyseCommand;
use Qossmic\Deptrac\Console\Command\AnalyseRunner;
use Qossmic\Deptrac\Console\Command\DebugLayerCommand;
use Qossmic\Deptrac\Console\Command\DebugLayerRunner;
use Qossmic\Deptrac\Console\Command\DebugTokenCommand;
use Qossmic\Deptrac\Console\Command\DebugTokenRunner;
use Qossmic\Deptrac\Console\Command\DebugUnassignedCommand;
use Qossmic\Deptrac\Console\Command\DebugUnassignedRunner;
use Qossmic\Deptrac\Console\Command\InitCommand;
use Qossmic\Deptrac\Dependency\DependencyResolver;
use Qossmic\Deptrac\Dependency\Emitter\ClassDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\ClassSuperglobalDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\FileDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\FunctionDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\FunctionSuperglobalDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\UsesDependencyEmitter;
use Qossmic\Deptrac\Dependency\InheritanceFlattener;
use Qossmic\Deptrac\Dependency\TokenResolver;
use Qossmic\Deptrac\File\Dumper;
use Qossmic\Deptrac\File\YmlFileLoader;
use Qossmic\Deptrac\InputCollector\FileInputCollector;
use Qossmic\Deptrac\InputCollector\InputCollectorInterface;
use Qossmic\Deptrac\Layer\Collector\BoolCollector;
use Qossmic\Deptrac\Layer\Collector\ClassNameCollector;
use Qossmic\Deptrac\Layer\Collector\ClassNameRegexCollector;
use Qossmic\Deptrac\Layer\Collector\CollectorProvider;
use Qossmic\Deptrac\Layer\Collector\CollectorResolver;
use Qossmic\Deptrac\Layer\Collector\CollectorResolverInterface;
use Qossmic\Deptrac\Layer\Collector\CollectorTypes;
use Qossmic\Deptrac\Layer\Collector\DirectoryCollector;
use Qossmic\Deptrac\Layer\Collector\ExtendsCollector;
use Qossmic\Deptrac\Layer\Collector\FunctionNameCollector;
use Qossmic\Deptrac\Layer\Collector\ImplementsCollector;
use Qossmic\Deptrac\Layer\Collector\InheritanceLevelCollector;
use Qossmic\Deptrac\Layer\Collector\InheritsCollector;
use Qossmic\Deptrac\Layer\Collector\LayerCollector;
use Qossmic\Deptrac\Layer\Collector\MethodCollector;
use Qossmic\Deptrac\Layer\Collector\SuperglobalCollector;
use Qossmic\Deptrac\Layer\Collector\UsesCollector;
use Qossmic\Deptrac\Layer\LayerResolver;
use Qossmic\Deptrac\Layer\LayerResolverInterface;
use Qossmic\Deptrac\OutputFormatter\BaselineOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\CodeclimateOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\FormatterProvider;
use Qossmic\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputDisplayFormatter;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputDotFormatter;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputHtmlFormatter;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputImageFormatter;
use Qossmic\Deptrac\OutputFormatter\JsonOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\JUnitOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\TableOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\XMLOutputFormatter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->defaults()
        ->autowire();

    /*
     * Utilities
     */
    $services->set(EventDispatcher::class);
    $services->alias(EventDispatcherInterface::class, EventDispatcher::class);
    $services->alias(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class, EventDispatcher::class);
    $services
        ->set(FileInputCollector::class)
        ->args([
            '$paths' => param('paths'),
            '$excludedFilePatterns' => param('exclude_files'),
            '$basePath' => param('projectDirectory'),
        ]);
    $services->alias(InputCollectorInterface::class, FileInputCollector::class);
    $services->set(YmlFileLoader::class);
    $services
        ->set(Dumper::class)
        ->args([
            '$templateFile' => __DIR__.'/deptrac_template.yaml',
        ]);

    /*
     * AST
     */
    $services->set(AstLoader::class);
    $services->set(ParserFactory::class);
    $services->set(Lexer::class);
    $services
        ->set(Parser::class)
        ->factory([service(ParserFactory::class), 'create'])
        ->args([
            '$kind' => ParserFactory::PREFER_PHP7,
        ]);
    $services->set(AstFileReferenceInMemoryCache::class);
    $services->alias(AstFileReferenceCacheInterface::class, AstFileReferenceInMemoryCache::class);
    $services
        ->set(NikicPhpParser::class)
        ->args([
            '$extractors' => tagged_iterator('reference_extractors'),
        ]);
    $services->alias(ParserInterface::class, NikicPhpParser::class);
    $services->set(TypeResolver::class);
    $services
        ->set(AnnotationReferenceExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(AnonymousClassExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(ClassConstantExtractor::class)
        ->tag('reference_extractors');

    /*
     * Dependency
     */
    $services
        ->set(DependencyResolver::class)
        ->args([
            '$config' => param('analyser'),
            '$emitterLocator' => tagged_locator('dependency_emitter', null, 'getAlias'),
        ]);
    $services->set(TokenResolver::class);
    $services->set(InheritanceFlattener::class);
    $services
        ->set(ClassDependencyEmitter::class)
        ->tag('dependency_emitter');
    $services
        ->set(ClassSuperglobalDependencyEmitter::class)
        ->tag('dependency_emitter');
    $services
        ->set(FileDependencyEmitter::class)
        ->tag('dependency_emitter');
    $services
        ->set(FunctionDependencyEmitter::class)
        ->tag('dependency_emitter');
    $services
        ->set(FunctionSuperglobalDependencyEmitter::class)
        ->tag('dependency_emitter');
    $services
        ->set(UsesDependencyEmitter::class)
        ->tag('dependency_emitter');

    /*
     * Layer
     */
    $services
        ->set(LayerResolver::class)
        ->args([
            '$layers' => param('layers'),
        ]);
    $services->alias(LayerResolverInterface::class, LayerResolver::class);
    $services->alias('layer_resolver.depender', LayerResolverInterface::class);
    $services->alias('layer_resolver.dependent', LayerResolverInterface::class);
    $services
        ->set(CollectorProvider::class)
        ->args([
            '$collectorLocator' => tagged_locator('collector', 'type'),
        ]);
    $services->set(CollectorResolver::class);
    $services->alias(CollectorResolverInterface::class, CollectorResolver::class);
    $services
        ->set(BoolCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_BOOL]);
    $services
        ->set(ClassNameCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_CLASS_NAME]);
    $services
        ->set(ClassNameRegexCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_CLASS_NAME_REGEX]);
    $services
        ->set(DirectoryCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_DIRECTORY]);
    $services
        ->set(ExtendsCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_EXTENDS]);
    $services
        ->set(FunctionNameCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_FUNCTION_NAME]);
    $services
        ->set(ImplementsCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_IMPLEMENTS]);
    $services
        ->set(InheritanceLevelCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_INHERITANCE]);
    $services
        ->set(InheritsCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_INHERITS]);
    $services
        ->set(LayerCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_LAYER]);
    $services
        ->set(MethodCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_METHOD]);
    $services
        ->set(SuperglobalCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_SUPERGLOBAL]);
    $services
        ->set(UsesCollector::class)
        ->tag('collector', ['type' => CollectorTypes::TYPE_USES]);

    /*
     * Analyser
     */
    $services->set(AstMapExtractor::class);
    $services
        ->set(UncoveredDependentHandler::class)
        ->args([
            '$ignoreUncoveredInternalClasses' => param('ignore_uncovered_internal_classes'),
        ])
        ->tag('event_listener', ['priority' => 32]);
    $services
        ->set(MatchingLayersHandler::class)
        ->tag('event_listener', ['priority' => 16]);
    $services
        ->set(AllowDependencyHandler::class)
        ->args([
            '$allowedLayers' => param('ruleset'),
        ])
        ->tag('event_listener', ['priority' => 4]);
    $services
        ->set(ViolationHandler::class)
        ->args([
            '$skippedViolations' => param('skip_violations'),
        ])
        ->tag('event_listener', ['method' => 'handleViolation', 'priority' => -32])
        ->tag('event_listener', ['method' => 'handleUnmatchedSkipped']);
    $services
        ->set(DependencyLayersAnalyser::class)
        ->args([
            '$dependerLayerResolver' => service('layer_resolver.depender'),
            '$dependentLayerResolver' => service('layer_resolver.dependent'),
        ]);
    $services->set(LegacyDependencyLayersAnalyser::class);
    $services->set(TokenInLayerAnalyser::class);
    $services->set(LayerForTokenAnalyser::class);
    $services->set(UnassignedTokenAnalyser::class);

    /*
     * OutputFormatter
     */
    $services
        ->set(FormatterConfiguration::class)
        ->args([
            '$config' => param('formatters'),
        ]);
    $services
        ->set(FormatterProvider::class)
        ->args([
            '$formatterLocator' => tagged_locator('output_formatter', null, 'getName'),
        ]);
    $services
        ->set(ConsoleOutputFormatter::class)
        ->tag('output_formatter');
    $services
        ->set(GithubActionsOutputFormatter::class)
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
        ->set(CodeclimateOutputFormatter::class)
        ->tag('output_formatter');

    /*
     * Console
     */
    $services
        ->set(InitCommand::class)
        ->autowire()
        ->tag('console.command');
    $services
        ->set(AnalyseRunner::class)
        ->autowire();
    $services
        ->set(AnalyseCommand::class)
        ->autowire()
        ->tag('console.command');
    $services
        ->set(DebugLayerRunner::class)
        ->autowire()
        ->args([
            '$layers' => param('layers'),
        ]);
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
