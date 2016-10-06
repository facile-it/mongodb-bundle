<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\DependencyInjection;

use Facile\MongoDbBundle\DataCollector\MongoDbDataCollector;
use Facile\MongoDbBundle\Services\ClientRegistry;
use Facile\MongoDbBundle\Services\ConnectionFactory;
use Facile\MongoDbBundle\Services\Loggers\MongoLogger;
use MongoDB\Database;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class MongoDbBundleExtension.
 * @internal
 */
class MongoDbBundleExtension extends Extension
{
    /** @var ContainerBuilder */
    private $containerBuilder;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->containerBuilder = $container;
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->defineLoggers();

        if ($container->getParameter("kernel.environment") === 'dev' && class_exists(WebProfilerBundle::class)) {
            $this->defineDataCollector();
        }

        $this->defineClientRegistry($config['clients'], $container->getParameter("kernel.environment"));
        $this->defineConnectionFactory();
        $this->defineConnections($config['connections']);

        return $config;
    }

    private function defineLoggers()
    {
        $loggerDefinition = new Definition(MongoLogger::class);
        $loggerDefinition->setPublic(false);

        $this->containerBuilder->setDefinition('facile_mongo_db.logger', $loggerDefinition);
    }

    private function defineDataCollector()
    {
        $dataCollectorDefinition = new Definition(MongoDbDataCollector::class);
        $dataCollectorDefinition->addMethodCall('setLogger', [new Reference('facile_mongo_db.logger')]);
        $dataCollectorDefinition->addTag(
            'data_collector',
            [
                'template' => 'FacileMongoDbBundle:Collector:mongo.html.twig',
                'id' => 'mongodb',
                'priority' => 250,
            ]
        );
        $dataCollectorDefinition->setPublic(false);

        $this->containerBuilder->setDefinition('facile_mongo_db.data_collector', $dataCollectorDefinition);
    }

    /**
     * @param array  $clientsConfig
     * @param string $environment
     */
    private function defineClientRegistry(array $clientsConfig, string $environment)
    {
        $clientRegistryDefinition = new Definition(
            ClientRegistry::class,
            [
                new Reference('facile_mongo_db.logger'),
                $environment,
            ]
        );
        $clientRegistryDefinition->addMethodCall('addClientsConfigurations', [$clientsConfig]);
        $clientRegistryDefinition->setPublic(false);

        $this->containerBuilder->setDefinition('mongo.client_registry', $clientRegistryDefinition);
    }

    private function defineConnectionFactory()
    {
        $factoryDefinition = new Definition(ConnectionFactory::class, [new Reference('mongo.client_registry')]);
        $factoryDefinition->setPublic(false);

        $this->containerBuilder->setDefinition('mongo.connection_factory', $factoryDefinition);
    }

    /**
     * @param array $connections
     */
    private function defineConnections(array $connections)
    {
        foreach ($connections as $name => $conf) {
            $connectionDefinition = new Definition(
                Database::class,
                [
                    $conf['client_name'],
                    $conf['database_name'],
                ]
            );
            $connectionDefinition->setFactory([new Reference('mongo.connection_factory'), 'createConnection']);
            $this->containerBuilder->setDefinition('mongo.connection.'.$name, $connectionDefinition);
        }
        $this->containerBuilder->setAlias('mongo.connection', 'mongo.connection.'.array_keys($connections)[0]);
    }
}
