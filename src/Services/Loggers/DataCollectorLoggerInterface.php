<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Loggers;

use Facile\MongoDbBundle\Models\Query;

interface DataCollectorLoggerInterface
{
    public function logQuery(Query $event);

    public function hasLoggedEvents(): bool;

    public function getLoggedEvent(): Query;

    public function addConnection(string $connection);

    /**
     * @return array|\string[]
     */
    public function getConnections(): array;
}
