<?php

declare(strict_types=1);

namespace MongoBundle\Services;

use MongoBundle\Models\ConnectionConfiguration;
use MongoDB\Client;
use MongoDB\Database;

/**
 * Class ConnectionFactory.
 */
class ConnectionFactory
{
    /**
     * @var Client[]
     */
    private $clients = [];

    /**
     * @param ConnectionConfiguration $configuration
     *
     * @return Database
     *
     */
    public function createConnection(ConnectionConfiguration $configuration): Database
    {
        // Define the client key to retrieve or create the client instance.
        $clientKey = sprintf('%s.%s', $configuration->getHost(), $configuration->getPort());
        if ($configuration->hasCredentials()) {
            $clientKey .= ".".$configuration->getUsername();
        }

        $client = $this->getClientForConfiguration($configuration, $clientKey);

        return $client->selectDatabase($configuration->getDatabase());
    }

    /**
     * @param ConnectionConfiguration $configuration
     *
     * @return Client
     */
    private function createClient(ConnectionConfiguration $configuration): Client
    {
        $uri = $this->prepareConnectionUri($configuration);
        $client = new Client($uri);

        return $client;
    }

    /**
     * @param ConnectionConfiguration $configuration
     * @param string                  $clientKey
     *
     * @return Client
     */
    private function getClientForConfiguration(ConnectionConfiguration $configuration, string $clientKey): Client
    {
        if (!array_key_exists($clientKey, $this->clients)) {
            $client = $this->createClient($configuration);
            $this->clients[$clientKey] = $client;

            return $client;
        }
        $client = $this->clients[$clientKey];

        return $client;
    }

    /**
     * @param ConnectionConfiguration $configuration
     *
     * @return string
     */
    private function prepareConnectionUri(ConnectionConfiguration $configuration)
    {
        $credentials = '';
        if ($configuration->hasCredentials()) {
            $credentials = sprintf('%s:%s@', $configuration->getUsername(), $configuration->getPassword());
        }
        
        $uri = sprintf(
            "mongodb://%s%s:%d/%s",
            $credentials,
            $configuration->getHost(),
            $configuration->getPort(),
            $configuration->getDatabase()
        );

        return $uri;
    }
}
