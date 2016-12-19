<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Loggers;

use Facile\MongoDbBundle\Models\QueryLog;

/**
 * Interface DataCollectorLoggerInterface
 */
interface DataCollectorLoggerInterface
{
    /**
     * @param QueryLog $event
     */
    public function logQuery(QueryLog $event);

    /**
     * @return bool
     */
    public function hasLoggedEvents(): bool;

    /**
     * @return QueryLog
     */
    public function getLoggedEvent(): QueryLog;

    /**
     * @param string $connection
     */
    public function addConnection(string $connection);

    /**
     * @return array|\string[]
     */
    public function getConnections(): array;
}
