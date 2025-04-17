<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use MongoDB\Database;

/**
 * Class ConnectionFactory.
 *
 * @internal
 */
final class ConnectionFactory
{
    public function __construct(private readonly ClientRegistry $clientRegistry) {}

    public function createConnection(string $clientName, string $databaseName): Database
    {
        return $this->clientRegistry
            ->getClientForDatabase($clientName, $databaseName)
            ->selectDatabase($databaseName);
    }
}
