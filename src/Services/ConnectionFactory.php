<?php

declare(strict_types = 1);

namespace MongoBundle\Services;

use MongoDB\Client;
use MongoDB\Database;

/**
 * Class ConnectionFactory
 */
class ConnectionFactory
{
    /**
     * @var Client[]
     */
    private $clients = [];

    /**
     * @param string $host
     * @param int    $port
     * @param string $database
     *
     * @return Database
     */
    public function createConnection(string $host, int $port, string $database): Database
    {
        // Define the client key to retrieve or create the client instance.
        $clientKey = sprintf('%s.%s', $host, $port);
        $client = $this->getClientForConfiguration($host, $port, $clientKey);

        return $client->selectDatabase($database);
    }

    /**
     * @param string $host
     * @param int    $port
     *
     * @return Client
     */
    private function createClient(string $host, int $port): Client
    {
        $uri = sprintf("mongodb://%s:%s", $host, $port);
        $client = new Client($uri);

        return $client;
    }

    /**
     * @param string $host
     * @param int    $port
     * @param string $clientKey
     *
     * @return Client
     */
    private function getClientForConfiguration(string $host, int $port, string $clientKey): Client
    {
        if (!array_key_exists($clientKey, $this->clients)) {
            $client = $this->createClient($host, $port);
            $this->clients[$clientKey] = $client;

            return $client;
        }
        $client = $this->clients[$clientKey];

        return $client;
    }
}
