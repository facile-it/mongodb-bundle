<?php

namespace MongoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mongo_bundle');
        $rootNode
            ->beforeNormalization()
            ->ifTrue(
                function ($v) {
                    return is_array($v) && !array_key_exists('connections', $v);
                }
            )
            ->then(
                function ($v) {
                    $excludedKeys = ['default_connection'];
                    $connection = [];
                    foreach ($v as $key => $value) {
                        if (in_array($key, $excludedKeys, true)) {
                            continue;
                        }
                        $connection[$key] = $v[$key];
                        unset($v[$key]);
                    }
                    $v['connections'] = ['default' => $connection];

                    return $v;
                }
            )
            ->end()
            ->children()
            ->scalarNode('default_connection')->info('If not defined, the first connection will be taken.')->end()
            ->arrayNode('connections')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
            ->scalarNode('host')
            ->isRequired()
            ->info('Your MongoDB host address')
            ->end()
            ->scalarNode('database')
            ->isRequired()
            ->info('Your MongoDB database name')
            ->end()
            ->integerNode('port')->defaultValue(27017)->end()
            ->scalarNode('username')->defaultValue('')->end()
            ->scalarNode('password')->defaultValue('')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
