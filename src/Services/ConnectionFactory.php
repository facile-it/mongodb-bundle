<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Models\ClientConfiguration;
use MongoDB\Client;
use MongoDB\Database;

/**
 * Class ConnectionFactory.
 */
class ConnectionFactory
{
    /** @var ClientRegistry */
    private $clientRegistry;

    /**
     * ConnectionFactory constructor.
     *
     * @param ClientRegistry $clientRegistry
     */
    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    /**
     * @param string $clientName
     * @param string $databaseName
     *
     * @return Database
     */
    public function createConnection(string $clientName, string $databaseName): Database
    {
        return $this->clientRegistry
            ->getClientForDatabase($clientName,$databaseName)
            ->selectDatabase($databaseName);
    }
}
