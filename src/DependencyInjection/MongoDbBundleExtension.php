<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\DependencyInjection;

use Facile\MongoDbBundle\Event\ConnectionEvent;
use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Services\ClientRegistry;
use Facile\MongoDbBundle\Services\ConnectionFactory;
use MongoDB\Database;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class MongoDbBundleExtension.
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
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->defineClientRegistry($config['clients'], $container->getParameter("kernel.environment"));
        $this->defineConnectionFactory();
        $this->defineConnections($config['connections']);

        if ($this->mustCollectData($config)) {
            $loader->load('profiler.xml');
            $this->attachDataCollectionListenerToEventManager();
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
        return 'dev' === $this->containerBuilder->getParameter("kernel.environment")
            && class_exists(WebProfilerBundle::class)
            && $config['data_collection'] === true;
    }

    /**
     * @param array $clientsConfig
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
            $this->containerBuilder->setDefinition('mongo.connection.' . $name, $connectionDefinition);
        }
        $this->containerBuilder->setAlias('mongo.connection', new Alias('mongo.connection.' . array_keys($connections)[0], true));
    }

    private function attachDataCollectionListenerToEventManager()
    {
        $eventManagerDefinition = $this->containerBuilder->getDefinition('facile_mongo_db.event_dispatcher');
        $eventManagerDefinition->addMethodCall(
            'addListener',
            [
                ConnectionEvent::CLIENT_CREATED,
                [new Reference('facile_mongo_db.data_collector.listener'), 'onConnectionClientCreated']
            ]
        );
        $eventManagerDefinition->addMethodCall(
            'addListener',
            [
                QueryEvent::QUERY_EXECUTED,
                [new Reference('facile_mongo_db.data_collector.listener'), 'onQueryExecuted']
            ]
        );
    }
}
