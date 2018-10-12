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
            ->booleanNode('data_collection')->defaultTrue()->info('Disables Data Collection if needed')
            ->end()
            ->arrayNode('clients')->isRequired()->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
            ->arrayNode('hosts')->info('Hosts addresses and ports')
            ->prototype('array')
            ->children()
            ->scalarNode('host')->isRequired()->end()
            ->integerNode('port')->defaultValue(27017)->end()
            ->end()
            ->end()
            ->end()
            ->scalarNode('username')->defaultValue('')->end()
            ->scalarNode('password')->defaultValue('')->end()
            ->scalarNode('authSource')->defaultValue(null)->info('Database name associated with the user’s credentials')->end()
            ->scalarNode('readPreference')
            ->defaultValue('primaryPreferred')
            ->validate()
            ->ifNotInArray($readPreferenceValidOptions)
            ->thenInvalid('Invalid readPreference option %s, must be one of [' . implode(", ", $readPreferenceValidOptions) . ']')
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
            ->scalarNode('client_name')->isRequired()->info('Desired client name')->end()
            ->scalarNode('database_name')->isRequired()->info('Database name')->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
