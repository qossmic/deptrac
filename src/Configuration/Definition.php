<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Definition implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('deptrac');
        $builder->getRootNode()
            ->children()
                ->arrayNode('imports')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('parameters')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('analyzer')
                    ->children()
                        ->scalarNode('count_use_statements')->defaultTrue()->end()
                        ->arrayNode('types')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
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
                ->arrayNode('layers')
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
                ->end()
                ->arrayNode('ruleset')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
                ->arrayNode('formatters')
                    ->children()
                        ->arrayNode('graphviz')
                            ->children()
                                ->arrayNode('hidden_layers')
                                    ->useAttributeAsKey('name')
                                    ->scalarPrototype()->end()
                                ->end()
                                ->arrayNode('groups')
                                    ->useAttributeAsKey('name')
                                    ->arrayPrototype()
                                        ->scalarPrototype()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('skip_violations')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
