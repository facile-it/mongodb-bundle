<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Loggers;

use Facile\MongoDbBundle\Models\LogEvent;

/**
 * Interface DataCollectorLoggerInterface
 */
interface DataCollectorLoggerInterface
{
    public function startLogging(LogEvent $event);
    
    /**
     * @param LogEvent $event
     */
    public function logQuery(LogEvent $event);

    /**
     * @return bool
     */
    public function hasLoggedEvents(): bool;

    /**
     * @return LogEvent
     */
    public function getLoggedEvent(): LogEvent;

    /**
     * @param string $connection
     */
    public function addConnection(string $connection);

    /**
     * @return array|\string[]
     */
    public function getConnections(): array;
}
