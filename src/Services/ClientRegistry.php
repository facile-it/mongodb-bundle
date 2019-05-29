<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Capsule\Client as BundleClient;
use Facile\MongoDbBundle\Event\ConnectionEvent;
use Facile\MongoDbBundle\Models\ClientConfiguration;
use MongoDB\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class ClientRegistry
{
    /** @var Client[] */
    private $clients;

    /** @var ClientConfiguration[] */
    private $configurations;

    /** @var string */
    private $environment;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, string $environment)
    {
        $this->clients = [];
        $this->configurations = [];
        $this->environment = $environment;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addClientsConfigurations(array $configurations)
    {
        foreach ($configurations as $name => $conf) {
            $this->addClientConfiguration($name, $conf);
        }
    }

    private function addClientConfiguration(string $name, array $conf)
    {
        $this->configurations[$name] = $this->buildClientConfiguration($conf);
    }

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
                'readPreference' => $conf['readPreference'],
            ]
        );
    }

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

    public function getClientForDatabase(string $name, string $databaseName): Client
    {
        return $this->getClient($name, $databaseName);
    }

    public function getClientNames(): array
    {
        return array_keys($this->clients);
    }

    /**
     * @param string $databaseName
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
            $this->clients[$clientKey] = $this->buildClient($name, $conf->getUri(), $options, []);

            $this->eventDispatcher->dispatch(
                ConnectionEvent::CLIENT_CREATED,
                new ConnectionEvent($clientKey)
            );
        }

        return $this->clients[$clientKey];
    }

    private function buildClient(string $clientName, string $uri, array $options, array $driverOptions): Client
    {
        if ('dev' === $this->environment) {
            return new BundleClient($uri, $options, $driverOptions, $clientName, $this->eventDispatcher);
        }

        return new Client($uri, $options, $driverOptions);
    }
}
