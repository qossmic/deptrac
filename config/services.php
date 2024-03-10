<?php

declare(strict_types=1);

use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qossmic\Deptrac\Contract\Analyser\EventHelper;
use Qossmic\Deptrac\Contract\Config\CollectorType;
use Qossmic\Deptrac\Contract\Config\EmitterType;
use Qossmic\Deptrac\Contract\Layer\LayerProvider;
use Qossmic\Deptrac\Core\Analyser\DependencyLayersAnalyser;
use Qossmic\Deptrac\Core\Analyser\EventHandler\AllowDependencyHandler;
use Qossmic\Deptrac\Core\Analyser\EventHandler\DependsOnDisallowedLayer;
use Qossmic\Deptrac\Core\Analyser\EventHandler\DependsOnInternalToken;
use Qossmic\Deptrac\Core\Analyser\EventHandler\DependsOnPrivateLayer;
use Qossmic\Deptrac\Core\Analyser\EventHandler\MatchingLayersHandler;
use Qossmic\Deptrac\Core\Analyser\EventHandler\UncoveredDependentHandler;
use Qossmic\Deptrac\Core\Analyser\EventHandler\UnmatchedSkippedViolations;
use Qossmic\Deptrac\Core\Analyser\LayerDependenciesAnalyser;
use Qossmic\Deptrac\Core\Analyser\LayerForTokenAnalyser;
use Qossmic\Deptrac\Core\Analyser\RulesetUsageAnalyser;
use Qossmic\Deptrac\Core\Analyser\TokenInLayerAnalyser;
use Qossmic\Deptrac\Core\Analyser\UnassignedTokenAnalyser;
use Qossmic\Deptrac\Core\Ast\AstLoader;
use Qossmic\Deptrac\Core\Ast\AstMapExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceCacheInterface;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\AnnotationReferenceExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\AnonymousClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassConstantExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassLikeExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\FunctionCallResolver;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\FunctionLikeExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\GroupUseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\InterfaceExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\KeywordExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\PropertyExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\StaticExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\UseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\VariableExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\ParserInterface;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanContainerDecorator;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanParser;
use Qossmic\Deptrac\Core\Dependency\DependencyResolver;
use Qossmic\Deptrac\Core\Dependency\Emitter\ClassDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\ClassSuperglobalDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\FileDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\FunctionCallDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\FunctionDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\FunctionSuperglobalDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\UsesDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\InheritanceFlattener;
use Qossmic\Deptrac\Core\Dependency\TokenResolver;
use Qossmic\Deptrac\Core\InputCollector\FileInputCollector;
use Qossmic\Deptrac\Core\InputCollector\InputCollectorInterface;
use Qossmic\Deptrac\Core\Layer\Collector\AttributeCollector;
use Qossmic\Deptrac\Core\Layer\Collector\BoolCollector;
use Qossmic\Deptrac\Core\Layer\Collector\ClassCollector;
use Qossmic\Deptrac\Core\Layer\Collector\ClassLikeCollector;
use Qossmic\Deptrac\Core\Layer\Collector\ClassNameRegexCollector;
use Qossmic\Deptrac\Core\Layer\Collector\CollectorProvider;
use Qossmic\Deptrac\Core\Layer\Collector\CollectorResolver;
use Qossmic\Deptrac\Core\Layer\Collector\CollectorResolverInterface;
use Qossmic\Deptrac\Core\Layer\Collector\ComposerCollector;
use Qossmic\Deptrac\Core\Layer\Collector\DirectoryCollector;
use Qossmic\Deptrac\Core\Layer\Collector\ExtendsCollector;
use Qossmic\Deptrac\Core\Layer\Collector\FunctionNameCollector;
use Qossmic\Deptrac\Core\Layer\Collector\GlobCollector;
use Qossmic\Deptrac\Core\Layer\Collector\ImplementsCollector;
use Qossmic\Deptrac\Core\Layer\Collector\InheritanceLevelCollector;
use Qossmic\Deptrac\Core\Layer\Collector\InheritsCollector;
use Qossmic\Deptrac\Core\Layer\Collector\InterfaceCollector;
use Qossmic\Deptrac\Core\Layer\Collector\LayerCollector;
use Qossmic\Deptrac\Core\Layer\Collector\MethodCollector;
use Qossmic\Deptrac\Core\Layer\Collector\PhpInternalCollector;
use Qossmic\Deptrac\Core\Layer\Collector\SuperglobalCollector;
use Qossmic\Deptrac\Core\Layer\Collector\TagValueRegexCollector;
use Qossmic\Deptrac\Core\Layer\Collector\TraitCollector;
use Qossmic\Deptrac\Core\Layer\Collector\UsesCollector;
use Qossmic\Deptrac\Core\Layer\LayerResolver;
use Qossmic\Deptrac\Core\Layer\LayerResolverInterface;
use Qossmic\Deptrac\Supportive\Console\Command\AnalyseCommand;
use Qossmic\Deptrac\Supportive\Console\Command\AnalyseRunner;
use Qossmic\Deptrac\Supportive\Console\Command\DebugDependenciesCommand;
use Qossmic\Deptrac\Supportive\Console\Command\DebugDependenciesRunner;
use Qossmic\Deptrac\Supportive\Console\Command\DebugLayerCommand;
use Qossmic\Deptrac\Supportive\Console\Command\DebugLayerRunner;
use Qossmic\Deptrac\Supportive\Console\Command\DebugTokenCommand;
use Qossmic\Deptrac\Supportive\Console\Command\DebugTokenRunner;
use Qossmic\Deptrac\Supportive\Console\Command\DebugUnassignedCommand;
use Qossmic\Deptrac\Supportive\Console\Command\DebugUnassignedRunner;
use Qossmic\Deptrac\Supportive\Console\Command\DebugUnusedCommand;
use Qossmic\Deptrac\Supportive\Console\Command\DebugUnusedRunner;
use Qossmic\Deptrac\Supportive\Console\Command\InitCommand;
use Qossmic\Deptrac\Supportive\File\Dumper;
use Qossmic\Deptrac\Supportive\File\YmlFileLoader;
use Qossmic\Deptrac\Supportive\OutputFormatter\BaselineOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\CodeclimateOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\FormatterConfiguration;
use Qossmic\Deptrac\Supportive\OutputFormatter\ConsoleOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\FormatterProvider;
use Qossmic\Deptrac\Supportive\OutputFormatter\GithubActionsOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\GraphVizOutputDisplayFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\GraphVizOutputDotFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\GraphVizOutputHtmlFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\GraphVizOutputImageFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\JsonOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\JUnitOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\TableOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\XMLOutputFormatter;
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

    $services
        ->set(PhpStanContainerDecorator::class)
        ->args([
            '$basePath' => param('projectDirectory'),
            '$paths' => param('paths'),
        ]);

    /*
     * Utilities
     */
    $services->set(EventDispatcher::class);
    $services->alias(EventDispatcherInterface::class, EventDispatcher::class);
    $services->alias(Symfony\Component\EventDispatcher\EventDispatcherInterface::class, EventDispatcher::class);
    $services->alias('event_dispatcher', EventDispatcher::class);
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
    $services
        ->set(PhpStanParser::class)
        ->args([
            '$extractors' => tagged_iterator('reference_extractors'),
        ]);
    $services->alias(ParserInterface::class, PhpStanParser::class);
    $services->set(TypeResolver::class);
    $services
        ->set(AnnotationReferenceExtractor::class)
        ->args([
            '$phpStanContainer' => service(PhpStanContainerDecorator::class)
               ])
        ->tag('reference_extractors');
    $services
        ->set(AnonymousClassExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(ClassConstantExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(FunctionLikeExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(PropertyExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(KeywordExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(StaticExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(FunctionLikeExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(VariableExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(FunctionCallResolver::class)
        ->tag('reference_extractors');
    $services
        ->set(ClassExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(ClassLikeExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(GroupUseExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(InterfaceExtractor::class)
        ->tag('reference_extractors');
    $services
        ->set(UseExtractor::class)
        ->tag('reference_extractors');

    /*
     * Dependency
     */
    $services
        ->set(DependencyResolver::class)
        ->args([
            '$config' => param('analyser'),
            '$emitterLocator' => tagged_locator('dependency_emitter', 'key'),
        ]);
    $services->set(TokenResolver::class);
    $services->set(InheritanceFlattener::class);
    $services
        ->set(ClassDependencyEmitter::class)
        ->tag('dependency_emitter', ['key' => EmitterType::CLASS_TOKEN->value]);
    $services
        ->set(ClassSuperglobalDependencyEmitter::class)
        ->tag('dependency_emitter', ['key' => EmitterType::CLASS_SUPERGLOBAL_TOKEN->value]);
    $services
        ->set(FileDependencyEmitter::class)
        ->tag('dependency_emitter', ['key' => EmitterType::FILE_TOKEN->value]);
    $services
        ->set(FunctionDependencyEmitter::class)
        ->tag('dependency_emitter', ['key' => EmitterType::FUNCTION_TOKEN->value]);
    $services
        ->set(FunctionCallDependencyEmitter::class)
        ->tag('dependency_emitter', ['key' => EmitterType::FUNCTION_CALL->value]);
    $services
        ->set(FunctionSuperglobalDependencyEmitter::class)
        ->tag('dependency_emitter', ['key' => EmitterType::FUNCTION_SUPERGLOBAL_TOKEN->value]);
    $services
        ->set(UsesDependencyEmitter::class)
        ->tag('dependency_emitter', ['key' => EmitterType::USE_TOKEN->value]);

    /*
     * Layer
     */
    $services
        ->set(LayerResolver::class)
        ->args([
            '$layers' => param('layers'),
        ]);
    $services->alias(LayerResolverInterface::class, LayerResolver::class);
    $services
        ->set(CollectorProvider::class)
        ->args([
            '$collectorLocator' => tagged_locator('collector', 'type'),
        ]);
    $services->set(CollectorResolver::class);
    $services->alias(CollectorResolverInterface::class, CollectorResolver::class);
    $services
        ->set(AttributeCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_ATTRIBUTE->value]);
    $services
        ->set(BoolCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_BOOL->value]);
    $services
        ->set(ClassCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_CLASS->value]);
    $services
        ->set(ClassLikeCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_CLASSLIKE->value]);
    $services
        ->set(ClassNameRegexCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_CLASS_NAME_REGEX->value]);
    $services
        ->set(TagValueRegexCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_TAG_VALUE_REGEX->value]);
    $services
        ->set(DirectoryCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_DIRECTORY->value]);
    $services
        ->set(ExtendsCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_EXTENDS->value]);
    $services
        ->set(FunctionNameCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_FUNCTION_NAME->value]);
    $services
        ->set(GlobCollector::class)
        ->args([
            '$basePath' => param('projectDirectory'),
        ])
        ->tag('collector', ['type' => CollectorType::TYPE_GLOB->value]);
    $services
        ->set(ImplementsCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_IMPLEMENTS->value]);
    $services
        ->set(InheritanceLevelCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_INHERITANCE->value]);
    $services
        ->set(InterfaceCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_INTERFACE->value]);
    $services
        ->set(InheritsCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_INHERITS->value]);
    $services
        ->set(LayerCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_LAYER->value]);
    $services
        ->set(MethodCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_METHOD->value]);
    $services
        ->set(SuperglobalCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_SUPERGLOBAL->value]);
    $services
        ->set(TraitCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_TRAIT->value]);
    $services
        ->set(UsesCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_USES->value]);
    $services
        ->set(PhpInternalCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_PHP_INTERNAL->value]);
    $services
        ->set(ComposerCollector::class)
        ->tag('collector', ['type' => CollectorType::TYPE_COMPOSER->value]);

    /*
     * Analyser
     */
    $services->set(AstMapExtractor::class);
    $services
        ->set(UncoveredDependentHandler::class)
        ->args([
            '$ignoreUncoveredInternalClasses' => param('ignore_uncovered_internal_classes'),
        ])
        ->tag('kernel.event_subscriber');
    $services
        ->set(MatchingLayersHandler::class)
        ->tag('kernel.event_subscriber');
    $services
        ->set(LayerProvider::class)
        ->args([
            '$allowedLayers' => param('ruleset'),
        ]);
    $services
        ->set(AllowDependencyHandler::class)
        ->tag('kernel.event_subscriber');
    $services
        ->set(DependsOnDisallowedLayer::class)
        ->tag('kernel.event_subscriber');
    $services
        ->set(DependsOnPrivateLayer::class)
        ->tag('kernel.event_subscriber');
    $services
        ->set(DependsOnInternalToken::class)
        ->tag('kernel.event_subscriber')
        ->args([
            '$config' => param('analyser'),
        ]);
    $services
        ->set(UnmatchedSkippedViolations::class)
        ->tag('kernel.event_subscriber');
    $services->set(EventHelper::class)
        ->args([
            '$skippedViolations' => param('skip_violations'),
        ]);
    $services
        ->set(DependencyLayersAnalyser::class);
    $services->set(TokenInLayerAnalyser::class)
        ->args([
            '$config' => param('analyser'),
        ]);
    $services->set(LayerForTokenAnalyser::class);
    $services->set(UnassignedTokenAnalyser::class)
        ->args([
            '$config' => param('analyser'),
        ]);
    $services->set(LayerDependenciesAnalyser::class);
    $services->set(RulesetUsageAnalyser::class)
        ->args([
            '$layers' => param('layers'),
        ]);

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
    $services
        ->set(DebugDependenciesRunner::class)
        ->autowire();
    $services
        ->set(DebugDependenciesCommand::class)
        ->autowire()
        ->tag('console.command');
    $services
        ->set(DebugUnusedRunner::class)
        ->autowire();
    $services
        ->set(DebugUnusedCommand::class)
        ->autowire()
        ->tag('console.command');
};
