<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Services\Loggers;

use Facile\MongoDbBundle\Services\Loggers\Model\LogEvent;

/**
 * Class NullLogger
 */
class NullLogger implements DataCollectorLoggerInterface
{
    /**
     * @param LogEvent $event
     */
    public function logQuery(LogEvent $event)
    {
    }

    /**
     * @return LogEvent
     */
    public function getLoggedEvent(): LogEvent
    {
        throw new \LogicException('No logged events from null logger!');
    }

    /**
     * @return bool
     */
    public function hasLoggedEvents(): bool
    {
        return false;
    }

    /**
     * @param string $connection
     */
    public function addConnection(string $connection)
    {
    }

    /**
     * @return array|\string[]
     */
    public function getConnections(): array
    {
        return [];
    }

    public function startLogging(LogEvent $event)
    {
    }
}
