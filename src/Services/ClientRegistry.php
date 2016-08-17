<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Capsule\Client;
use Facile\MongoDbBundle\Models\ClientConfiguration;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;

/**
 * Class ClientRegistry.
 */
class ClientRegistry
{
    /** @var Client[] */
    private $clients;
    /** @var ClientConfiguration[] */
    private $configurations;
    /** @var DataCollectorLoggerInterface */
    private $logger;

    /**
     * ClientRegistry constructor.
     *
     * @param DataCollectorLoggerInterface $logger
     */
    public function __construct(DataCollectorLoggerInterface $logger)
    {
        $this->clients = [];
        $this->configurations = [];
        $this->logger = $logger;
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
            $this->clients[$clientKey] = new Client($uri, $options, [], $this->logger);
            $this->logger->addConnection($clientKey);
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
