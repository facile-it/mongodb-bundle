<?php

namespace Facile\MongoDbBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $readPreferenceValidOptions = ['primary', 'primaryPreferred', 'secondary', 'secondaryPreferred', 'nearest'];

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mongo_db_bundle');
        $rootNode
            ->children()
            ->booleanNode('data_collection')->defaultTrue()->info('Use to disable Data Collection if needed')
            ->end()
            ->arrayNode('clients')->isRequired()->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype('array')
                ->children()
                    ->arrayNode('hosts')->info('Your MongoDB hosts addresses and ports')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('host')->isRequired()->end()
                                ->integerNode('port')->defaultValue(27017)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->scalarNode('username')->defaultValue('')->end()
                    ->scalarNode('password')->defaultValue('')->end()
                    ->scalarNode('readPreference')
                        ->defaultValue('primaryPreferred')
                        ->validate()
                            ->ifNotInArray($readPreferenceValidOptions)
                            ->thenInvalid('Invalid readPreference option %s, must be one of ['.implode(", ", $readPreferenceValidOptions).']')
                        ->end()
                    ->end()
                    ->scalarNode('replicaSet')->defaultValue(null)->end()
                    ->booleanNode('ssl')->defaultValue(false)->end()
                    ->integerNode('connectTimeoutMS')->defaultValue(null)->end();
        $rootNode
            ->children()
            ->arrayNode('connections')->isRequired()->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype('array')
                ->children()
                    ->scalarNode('client_name')->isRequired()->info('Your defined client name')->end()
                    ->scalarNode('database_name')->isRequired()->info('Your MongoDB database name')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
