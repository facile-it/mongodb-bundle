<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Models\ConnectionConfiguration;
use MongoDB\Client;
use MongoDB\Database;

/**
 * Class ConnectionFactory.
 */
final class ConnectionFactory
{
    /** @var ConnectionRegistry */
    private $registry;

    /**
     * ConnectionFactory constructor.
     *
     * @param ConnectionRegistry $registry
     */
    public function __construct(ConnectionRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param ConnectionConfiguration $configuration
     * @param string                  $connectionKey
     *
     * @return Database
     */
    public function createConnection(ConnectionConfiguration $configuration, string $connectionKey): Database
    {
        $client = $this->getClientForConfiguration($configuration, $connectionKey);

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
        if (!$this->registry->hasClient($clientKey)) {
            $client = $this->createClient($configuration);
            $this->registry->addClient($clientKey, $client);

            return $client;
        }

        return $this->registry->getClient($clientKey);
    }
}
