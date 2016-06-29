<?php

namespace MongoBundle\DependencyInjection;

use MongoBundle\Models\ConnectionConfiguration;
use MongoDB\Client;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class MongoBundleExtension
 */
class MongoBundleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('factory.xml');
        // If the default connection if not defined, get the first one.
        $defaultConnection = isset($config['default_connection']) ? $config['default_connection'] : key(
            $config['connections']
        );
        $this->buildConnections($container, $config);
        $this->setDefaultConnectionAlias($container, $defaultConnection);

        return $config;
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $connection The connection name
     * @param array            $config The connection configuration
     */
    private function createConnection(ContainerBuilder $container, $connection, array $config)
    {
        $configuration = new ConnectionConfiguration(
            $config['host'],
            $config['port'],
            $config['database'],
            $config['username'],
            $config['password']
        );
        // Create the connection based from the abstract one.
        $connectionDefinition = new Definition(Client::class, [$configuration]);
        $connectionDefinition->setFactory([new Reference('mongo.connection_factory'), 'createConnection']);
        // E.g.: mongo.connection.default
        $container->setDefinition('mongo.connection.'.$connection, $connectionDefinition);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function buildConnections(ContainerBuilder $container, array $config)
    {
        foreach ($config['connections'] as $connection => $connectionConfig) {
            $this->createConnection($container, $connection, $connectionConfig);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $defaultConnection
     */
    private function setDefaultConnectionAlias(ContainerBuilder $container, string $defaultConnection)
    {
        $container->setAlias('mongo.connection', 'mongo.connection.'.$defaultConnection);
    }
}
