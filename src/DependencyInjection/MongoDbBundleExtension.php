<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\DependencyInjection;

use Facile\MongoDbBundle\Command\DropCollectionCommand;
use Facile\MongoDbBundle\Command\DropDatabaseCommand;
use Facile\MongoDbBundle\Command\LoadFixturesCommand;
use Facile\MongoDbBundle\DataCollector\MongoDbDataCollector;
use Facile\MongoDbBundle\Event\ConnectionEvent;
use Facile\MongoDbBundle\Event\Listener\DataCollectorListener;
use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Services\ClientRegistry;
use Facile\MongoDbBundle\Services\ConnectionFactory;
use Facile\MongoDbBundle\Services\Explain\ExplainQueryService;
use Facile\MongoDbBundle\Services\Loggers\MongoQueryLogger;
use Facile\MongoDbBundle\Twig\FacileMongoDbBundleExtension;
use MongoDB\Database;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class MongoDbBundleExtension.
 *
 * @internal
 */
final class MongoDbBundleExtension extends Extension
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

        $this->defineEventManager();
        $this->defineClientRegistry($config['clients'], $container->getParameter('kernel.environment'));
        $this->defineConnectionFactory();
        $this->defineConnections($config['connections']);
        $this->defineCommands();

        if ($this->mustCollectData($config)) {
            $this->defineLoggers();
            $this->defineDataCollectorListeners();
            $this->attachDataCollectionListenerToEventManager();
            $this->defineDataCollector();
            $this->attachTwigExtesion();
            $this->defineExplainQueryService();
        }

        return $config;
    }

    /**
     * @param array $config
     *
     * @return bool
     */
    private function mustCollectData(array $config): bool
    {
        return in_array($this->containerBuilder->getParameter('kernel.environment'), ['dev'])
            && class_exists(WebProfilerBundle::class)
            && true === $config['data_collection'];
    }

    private function defineLoggers()
    {
        $loggerDefinition = new Definition(MongoQueryLogger::class);
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
                new Reference('facile_mongo_db.event_dispatcher'),
                $environment,
            ]
        );
        $clientRegistryDefinition->addMethodCall('addClientsConfigurations', [$clientsConfig]);
        $clientRegistryDefinition->setPublic(true);

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
            $connectionDefinition->setPublic(true);
            $this->containerBuilder->setDefinition('mongo.connection.'.$name, $connectionDefinition);
        }
        $this->containerBuilder->setAlias('mongo.connection', new Alias('mongo.connection.'.array_keys($connections)[0], true));
    }

    private function defineEventManager()
    {
        $eventManagerDefinition = new Definition(EventDispatcher::class);
        $eventManagerDefinition->setPublic(false);

        $this->containerBuilder->setDefinition('facile_mongo_db.event_dispatcher', $eventManagerDefinition);
    }

    private function defineDataCollectorListeners()
    {
        $dataCollectorListenerDefinition = new Definition(
            DataCollectorListener::class,
            [
                new Reference('facile_mongo_db.logger'),
            ]
        );
        $dataCollectorListenerDefinition->setPublic(false);

        $this->containerBuilder->setDefinition('facile_mongo_db.data_collector.listener', $dataCollectorListenerDefinition);
    }

    private function attachDataCollectionListenerToEventManager()
    {
        $eventManagerDefinition = $this->containerBuilder->getDefinition('facile_mongo_db.event_dispatcher');
        $eventManagerDefinition->addMethodCall(
            'addListener',
            [
                ConnectionEvent::CLIENT_CREATED,
                [new Reference('facile_mongo_db.data_collector.listener'), 'onConnectionClientCreated'],
            ]
        );
        $eventManagerDefinition->addMethodCall(
            'addListener',
            [
                QueryEvent::QUERY_EXECUTED,
                [new Reference('facile_mongo_db.data_collector.listener'), 'onQueryExecuted'],
            ]
        );
    }

    private function attachTwigExtesion()
    {
        $extension = new Definition(FacileMongoDbBundleExtension::class);
        $extension->setPublic(false);
        $extension->addTag('twig.extension');

        $this->containerBuilder->setDefinition('facile_mongo_db.twig_extesion', $extension);
    }

    private function defineExplainQueryService()
    {
        $explainServiceDefinition = new Definition(
            ExplainQueryService::class,
            [new Reference('mongo.client_registry')]
        );
        $explainServiceDefinition->setPublic(true);

        $this->containerBuilder->setDefinition('mongo.explain_query_service', $explainServiceDefinition);
    }

    private function defineCommands()
    {
        $commandClasses = [
            DropCollectionCommand::class,
            DropDatabaseCommand::class,
            LoadFixturesCommand::class,
        ];

        foreach ($commandClasses as $command) {
            $this->containerBuilder->setDefinition($command, new Definition($command))
                ->addTag('console.command');
        }
    }
}
