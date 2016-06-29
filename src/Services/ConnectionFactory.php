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
     */
    public function createConnection(ConnectionConfiguration $configuration): Database
    {
        $clientKey = $configuration->getConnectionIdentifier();
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
        return new Client($configuration->getConnectionUri());
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

        return $this->clients[$clientKey];
    }
}
