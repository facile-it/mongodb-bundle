<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Loggers;

use Facile\MongoDbBundle\Models\Query;

/**
 * Interface DataCollectorLoggerInterface
 */
interface DataCollectorLoggerInterface
{
    /**
     * @param Query $event
     */
    public function logQuery(Query $event);

    /**
     * @return bool
     */
    public function hasLoggedEvents(): bool;

    /**
     * @return Query
     */
    public function getLoggedEvent(): Query;

    /**
     * @param string $connection
     */
    public function addConnection(string $connection);

    /**
     * @return array|\string[]
     */
    public function getConnections(): array;
}
