<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\DependencyInjection;

use Qossmic\Deptrac\Dependency\Emitter\EmitterTypes;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @psalm-suppress all
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('deptrac');
        $rootNode = $builder->getRootNode();

        $this->appendAnalysedPaths($rootNode);
        $this->appendExcludePatterns($rootNode);
        $this->appendLayers($rootNode);
        $this->appendRuleset($rootNode);
        $this->appendSkippedViolations($rootNode);
        $this->appendFormatters($rootNode);
        $this->appendEmitterTypes($rootNode);
        $this->appendIgnoreUncoveredInternalClasses($rootNode);
        $this->appendUseRelativePathFromDepfile($rootNode);

        return $builder;
    }

    private function appendAnalysedPaths(ArrayNodeDefinition $node): void
    {
        $node
            ->fixXmlConfig('path')
            ->children()
                ->arrayNode('paths')
                    ->info('List of paths to search for PHP files to be analysed.')
                    ->addDefaultChildrenIfNoneSet()
                    ->requiresAtLeastOneElement()
                    ->scalarPrototype()
                        ->cannotBeEmpty()
                        ->defaultValue('src/')
                    ->end()
                ->end()
            ->end();
    }

    private function appendExcludePatterns(ArrayNodeDefinition $node): void
    {
        $node
            ->fixXmlConfig('exclude_file')
            ->children()
                ->arrayNode('exclude_files')
                    ->info('List of regular expressions for excluding files or directories from being analysed')
                    ->example('#.*Test\.php$#')
                    ->scalarPrototype()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();
    }

    private function appendLayers(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('layers')
                    ->useAttributeAsKey('name', false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('collectors')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->arrayPrototype()
                                    ->ignoreExtraKeys(false)
                                    ->children()
                                        ->scalarNode('type')->isRequired()->end()
                                        ->arrayNode('attributes')
                                            ->variablePrototype()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('attributes')
                                ->variablePrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function appendRuleset(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('ruleset')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
    }

    private function appendSkippedViolations(ArrayNodeDefinition $node): void
    {
        $node
            ->fixXmlConfig('skip_violation')
            ->children()
                ->arrayNode('skip_violations')
                    ->info('Skip violations matching a regular expressions')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
    }

    private function appendFormatters(ArrayNodeDefinition $node): void
    {
        $node
            ->fixXmlConfig('formatter')
            ->children()
                ->arrayNode('formatters')
                    ->info('Configure formatters')
                    ->variablePrototype()->end()
                ->end()
            ->end();
    }

    private function appendEmitterTypes(ArrayNodeDefinition $node): void
    {
        $emitterTypes = [
            EmitterTypes::CLASS_TOKEN,
            EmitterTypes::CLASS_SUPERGLOBAL_TOKEN,
            EmitterTypes::FILE_TOKEN,
            EmitterTypes::FUNCTION_TOKEN,
            EmitterTypes::FUNCTION_SUPERGLOBAL_TOKEN,
            EmitterTypes::USE_TOKEN,
        ];

        $node
            ->children()
                ->arrayNode('analyser')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('types')
                            ->isRequired()
                            ->defaultValue([EmitterTypes::CLASS_TOKEN, EmitterTypes::USE_TOKEN])
                            ->scalarPrototype()
                                ->beforeNormalization()
                                    ->ifNotInArray($emitterTypes)
                                    ->thenInvalid('Invalid type %s')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function appendIgnoreUncoveredInternalClasses(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->booleanNode('ignore_uncovered_internal_classes')
                    ->defaultTrue()
                ->end()
            ->end();
    }

    private function appendUseRelativePathFromDepfile(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->booleanNode('use_relative_path_from_depfile')
                    ->defaultTrue()
                ->end()
            ->end();
    }
}
