<?php

namespace Facile\MongoDbBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    const READ_PREFERENCE_VALID_OPTIONS = ['primary', 'primaryPreferred', 'secondary', 'secondaryPreferred', 'nearest'];

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mongo_db_bundle');
        $rootBuilder = \method_exists(TreeBuilder::class, 'getRootNode')
            ? $treeBuilder->getRootNode()
            : $treeBuilder->root('mongo_db_bundle');

        $this->addDataCollection($rootBuilder->children());
        $this->addClients($rootBuilder->children());
        $this->addConnections($rootBuilder->children());

        return $treeBuilder;
    }

    private function addDataCollection(NodeBuilder $builder)
    {
        $builder
            ->booleanNode('data_collection')
            ->defaultTrue()
            ->info('Disables Data Collection if needed');
    }

    private function addClients(NodeBuilder $builder)
    {
        $clientsBuilder = $builder
            ->arrayNode('clients')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children();

        $this->addClientsHosts($clientsBuilder);

        $clientsBuilder
            ->scalarNode('uri')
            ->defaultNull()
            ->info('Overrides hosts configuration');

        $clientsBuilder
            ->scalarNode('username')
            ->defaultValue('');

        $clientsBuilder
            ->scalarNode('password')
            ->defaultValue('');

        $clientsBuilder
            ->scalarNode('authSource')
            ->defaultNull()
            ->info('Database name associated with the user’s credentials');

        $clientsBuilder
            ->scalarNode('readPreference')
            ->defaultValue('primaryPreferred')
            ->validate()
            ->ifNotInArray(self::READ_PREFERENCE_VALID_OPTIONS)
            ->thenInvalid('Invalid readPreference option %s, must be one of [' . implode(', ', self::READ_PREFERENCE_VALID_OPTIONS) . ']');

        $clientsBuilder
            ->scalarNode('replicaSet')
            ->defaultNull();

        $clientsBuilder
            ->booleanNode('ssl')
            ->defaultFalse();

        $clientsBuilder
            ->integerNode('connectTimeoutMS')
            ->defaultNull();
    }

    private function addClientsHosts(NodeBuilder $builder)
    {
        $hostsBuilder = $builder
            ->arrayNode('hosts')
            ->info('Hosts addresses and ports')
            ->prototype('array')
            ->children();

        $hostsBuilder
            ->scalarNode('host')
            ->isRequired();

        $hostsBuilder
            ->integerNode('port')
            ->defaultValue(27017);
    }

    private function addConnections(NodeBuilder $builder)
    {
        $connectionBuilder = $builder
            ->arrayNode('connections')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children();

        $connectionBuilder
            ->scalarNode('client_name')
            ->isRequired()
            ->info('Desired client name');

        $connectionBuilder
            ->scalarNode('database_name')
            ->isRequired()
            ->info('Database name');
    }
}
