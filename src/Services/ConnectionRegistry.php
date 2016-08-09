<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use MongoDB\Client;
use Facile\MongoDbBundle\Exceptions\ClientNotFoundException;
use Facile\MongoDbBundle\Exceptions\ExistantClientException;

/**
 * Class ConnectionRegistry.
 */
final class ConnectionRegistry
{
    /** @var Client[] */
    private $connections;

    /**
     * ConnectionRegistry constructor.
     */
    public function __construct()
    {
        $this->connections = [];
    }

    /**
     * @param string $clientIdentifier
     *
     * @return Client
     *
     * @throws ClientNotFoundException
     */
    public function getClient(string $clientIdentifier): Client
    {
        if (isset($this->connections[$clientIdentifier])) {
            return $this->connections[$clientIdentifier];
        }

        throw new ClientNotFoundException(sprintf('Client for key %s not found', $clientIdentifier));
    }

    /**
     * @param string $clientIdentifier
     * @param Client $client
     *
     * @throws ExistantClientException
     */
    public function addClient(string $clientIdentifier, Client $client)
    {
        if (isset($this->connections[$clientIdentifier])) {
            throw new ExistantClientException('Client for key %s already exists');
        }

        $this->connections[$clientIdentifier] = $client;
    }

    /**
     * @param string $clientIdentifier
     *
     * @return bool
     */
    public function hasClient(string $clientIdentifier): bool
    {
        return array_key_exists($clientIdentifier, $this->connections);
    }
}
