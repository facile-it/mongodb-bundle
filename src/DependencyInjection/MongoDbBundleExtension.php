<?php

namespace Facile\MongoDbBundle\DependencyInjection;

use Facile\MongoDbBundle\Models\ConnectionConfiguration;
use MongoDB\Database;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class MongoDbBundleExtension.
 */
class MongoDbBundleExtension extends Extension
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
        $defaultConnection = isset($config['default_connection']) ?
            $config['default_connection'] :
            key($config['connections']);
        $this->buildConnections($container, $config);
        $this->setDefaultConnectionAlias($container, $defaultConnection);

        return $config;
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $connection The connection name
     * @param array            $config     The connection configuration
     */
    private function createConnection(ContainerBuilder $container, $connection, array $config)
    {
        $confDefinitionKey = $this->prepareConfigurationDefinition($container, $config);
        $connectionDefinition = new Definition(
            Database::class,
            [
                new Reference($confDefinitionKey),
                $this->makeConnectionIdentifier($config),
            ]
        );
        $connectionDefinition->setFactory([new Reference('mongo.connection_factory'), 'createConnection']);
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

    /**
     * @param array $config
     * @param bool  $dbName
     *
     * @return string
     */
    private function makeConnectionIdentifier(array $config, bool $dbName = false): string
    {
        $key = sprintf('%s.%d', $config['host'], $config['port']);
        $key .= !empty($config['username']) ? '.'.$config['username'] : '';
        $key .= $dbName ? '.'.$config['database'] : '';

        return $key;
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @return string
     */
    private function prepareConfigurationDefinition(ContainerBuilder $container, array $config): string
    {
        $confDefinition = new Definition(
            ConnectionConfiguration::class,
            [
                $config['host'],
                $config['port'],
                $config['database'],
                $config['username'],
                $config['password'],
            ]
        );
        $confDefinition->setPublic(false);

        $confDefinitionKey = 'mongo.connection_configuration'.$this->makeConnectionIdentifier($config, true);
        $container->setDefinition($confDefinitionKey, $confDefinition);

        return $confDefinitionKey;
    }
}
