<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Capsule\Client as BundleClient;
use Facile\MongoDbBundle\Event\ConnectionEvent;
use Facile\MongoDbBundle\Models\ClientConfiguration;
use MongoDB\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

/**
 * Class ClientRegistry.
 *
 * @internal
 */
final class ClientRegistry
{
    /** @var Client[] */
    private $clients;

    /** @var ClientConfiguration[] */
    private $configurations;

    /** @var bool */
    private $debug;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var DriverOptionsInterface  */
    private $driverOptions;

    /**
     * ClientRegistry constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DriverOptionsInterface $driverOptions
     * @param bool $debug
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DriverOptionsInterface $driverOptions,
        bool $debug)
    {
        $this->clients = [];
        $this->configurations = [];
        $this->debug = $debug;
        $this->eventDispatcher = $eventDispatcher;
        $this->driverOptions = $driverOptions;
    }

    /**
     * @param array $configurations
     */
    public function addClientsConfigurations(array $configurations)
    {
        foreach ($configurations as $name => $conf) {
            $this->addClientConfiguration($name, $conf);
        }
    }

    /**
     * @param string $name
     * @param array  $conf
     */
    private function addClientConfiguration(string $name, array $conf)
    {
        $this->configurations[$name] = $this->buildClientConfiguration($conf);
    }

    /**
     * @param array $conf
     *
     * @return ClientConfiguration
     */
    private function buildClientConfiguration(array $conf): ClientConfiguration
    {
        if (! $conf['uri']) {
            $conf['uri'] = $this->buildConnectionUri($conf['hosts']);
        }

        return new ClientConfiguration(
            $conf['uri'],
            $conf['username'],
            $conf['password'],
            $conf['authSource'],
            [
                'replicaSet' => $conf['replicaSet'],
                'ssl' => $conf['ssl'],
                'connectTimeoutMS' => $conf['connectTimeoutMS'],
                'readPreference' => $conf['readPreference']
            ],
            $this->driverOptions->buildDriverOptions($conf)
        );
    }

    /**
     * @param array $hosts
     *
     * @return string
     */
    private function buildConnectionUri(array $hosts): string
    {
        return 'mongodb://' . implode(
            ',',
            array_map(
                function (array $host) {
                    return sprintf('%s:%d', $host['host'], $host['port']);
                },
                $hosts
            )
        );
    }

    /**
     * @param string $name
     * @param string $databaseName
     *
     * @return Client
     */
    public function getClientForDatabase(string $name, string $databaseName): Client
    {
        return $this->getClient($name, $databaseName);
    }

    /**
     * @return array
     */
    public function getClientNames(): array
    {
        return array_keys($this->clients);
    }

    /**
     * @param string $name
     * @param string $databaseName
     *
     * @return Client
     */
    public function getClient(string $name, string $databaseName = null): Client
    {
        $clientKey = null !== $databaseName ? $name . '.' . $databaseName : $name;

        if (! isset($this->clients[$clientKey])) {
            $conf = $this->configurations[$name];
            $options = array_merge(
                [
                    'database' => $databaseName,
                    'authSource' => $conf->getAuthSource() ?? $databaseName ?? 'admin',
                ],
                $conf->getOptions()
            );
            $this->clients[$clientKey] = $this->buildClient($name, $conf->getUri(), $options, $conf->getDriverOptions());

            $event = new ConnectionEvent($clientKey);
            if (class_exists(LegacyEventDispatcherProxy::class)) {
                $this->eventDispatcher->dispatch($event, ConnectionEvent::CLIENT_CREATED);
            } else {
                $this->eventDispatcher->dispatch(ConnectionEvent::CLIENT_CREATED, $event);
            }
        }

        return $this->clients[$clientKey];
    }

    /**
     * @param string $clientName
     * @param string $uri
     * @param array  $options
     * @param array  $driverOptions
     *
     * @return Client
     */
    private function buildClient(string $clientName, string $uri, array $options, array $driverOptions): Client
    {
        if (true === $this->debug) {
            return new BundleClient($uri, $options, $driverOptions, $clientName, $this->eventDispatcher);
        }

        return new Client($uri, $options, $driverOptions);
    }
}
