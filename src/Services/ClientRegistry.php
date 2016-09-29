<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Capsule\Client as LoggerClient;
use Facile\MongoDbBundle\Models\ClientConfiguration;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use MongoDB\Client;

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
    /** @var string */
    private $environment;

    /**
     * ClientRegistry constructor.
     *
     * @param DataCollectorLoggerInterface $logger
     * @param string                       $environment
     */
    public function __construct(DataCollectorLoggerInterface $logger, string $environment)
    {
        $this->clients = [];
        $this->configurations = [];
        $this->logger = $logger;
        $this->environment = $environment;
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
            $options = array_merge(['database' => $databaseName], $conf->getOptions());
            $this->clients[$clientKey] = $this->buildClient($uri, $options, []);
            $this->logger->addConnection($clientKey);
        }

        return $this->clients[$clientKey];
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
     * @param                              $uri
     * @param array                        $options
     * @param array                        $driverOptions
     *
     * @return Client
     */
    private function buildClient($uri, array $options, array $driverOptions): Client
    {
        if ('dev' === $this->environment) {
            return new LoggerClient($uri, $options, $driverOptions, $this->logger);
        }

        return new Client($uri, $options, $driverOptions);
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
