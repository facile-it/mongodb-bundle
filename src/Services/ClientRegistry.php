<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Capsule\Client;
use Facile\MongoDbBundle\Models\ClientConfiguration;

/**
 * Class ClientRegistry.
 */
class ClientRegistry
{
    /** @var Client[] */
    private $clients;
    /** @var ClientConfiguration[] */
    private $configurations;

    /**
     * ClientRegistry constructor.
     */
    public function __construct()
    {
        $this->clients = [];
        $this->configurations = [];
    }

    /**
     * @param string $name
     * @param array  $conf
     */
    public function addClientConfiguration(string $name, array $conf)
    {
        $this->configurations[$name] = $this->buildClientConfiguration($conf);
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
     * @param string $name
     * @param string $databaseName
     *
     * @return Client
     */
    public function getClient(string $name, string $databaseName = null): Client
    {
        $clientKey = !is_null($databaseName) ? $name.'.'.$databaseName : $name;

        if (!isset($this->clients[$clientKey])) {
            $conf = $this->configurations[$name];
            $uri = sprintf('mongodb://%s:%d', $conf->getHost(), $conf->getPort());
            $options = array_merge(['db' => $databaseName], $conf->getOptions());
            $this->clients[$clientKey] = new Client($uri, $options);
        }

        return $this->clients[$clientKey];
    }

    /**
     * @param array $conf
     *
     * @return ClientConfiguration
     */
    private function buildClientConfiguration(array $conf): ClientConfiguration
    {
        return new ClientConfiguration(
            $conf['host'],
            $conf['port'],
            $conf['username'],
            $conf['password'],
            [
                'replicaSet' => $conf['replicaSet'],
                'ssl' => $conf['ssl'],
                'connectTimeoutMS' => $conf['connectTimeoutMS'],
            ]
        );
    }
}
