<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Definition implements ConfigurationInterface
{
    private function getImports(): ArrayNodeDefinition
    {
        $builder = new TreeBuilder('imports');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $builder->getRootNode();
        $rootNode
            ->scalarPrototype()->end()
        ;

        return $rootNode;
    }

    private function getServices(): ArrayNodeDefinition
    {
        $builder = new TreeBuilder('services');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $builder->getRootNode();
        $rootNode
            ->arrayPrototype()
                ->children()
                    ->scalarNode('class')->end()
                    ->booleanNode('autowire')->end()
                    ->arrayNode('tags')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $rootNode;
    }

    private function getParametersLayers(): ArrayNodeDefinition
    {
        $builder = new TreeBuilder('layers');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $builder->getRootNode();
        $rootNode
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name', $removeKeyItem = false)
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
                            ->ignoreExtraKeys($remove = false)
                            ->children()
                            ->scalarNode('type')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $rootNode;
    }

    private function getParametersFormatters(): ArrayNodeDefinition
    {
        $builder = new TreeBuilder('formatters');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $builder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('graphviz')
                    ->children()
                        ->arrayNode('hidden_layers')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('groups')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                        ->booleanNode('pointToGroups')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $rootNode;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('deptrac');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $builder->getRootNode();
        $rootNode
            ->append($this->getImports())
            ->append($this->getServices())
            ->children()
                ->arrayNode('parameters')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('paths')
                    ->requiresAtLeastOneElement()
                    ->scalarPrototype()->cannotBeEmpty()->end()
                ->end()
                ->arrayNode('exclude_files')
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode('baseline')->end()
                ->booleanNode('ignore_uncovered_internal_classes')->defaultTrue()->end()
                ->booleanNode('use_relative_path_from_depfile')->defaultTrue()->end()
                ->append($this->getParametersLayers())
                ->arrayNode('ruleset')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
                ->append($this->getParametersFormatters())
                ->arrayNode('skip_violations')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
