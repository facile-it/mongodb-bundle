<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Loggers;

use Facile\MongoDbBundle\Models\LogEvent;

/**
 * Class MongoLogger
 */
class MongoLogger implements DataCollectorLoggerInterface
{
    /** @var \SplQueue|LogEvent[] */
    private $logs;

    /** @var array|string[] */
    private $connections;

    /**
     * MongoLogger constructor.
     */
    public function __construct()
    {
        $this->logs = new \SplQueue();
        $this->connections = [];
    }

    public function startLogging(LogEvent $event)
    {
        $event->setStart(microtime(true));
    }

    /**
     * @param string $connection
     */
    public function addConnection(string $connection)
    {
        $this->connections[] = $connection;
    }

    /**
     * @return array|\string[]
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * @param LogEvent $event
     */
    public function logQuery(LogEvent $event)
    {
        $executionTime = microtime(true) - $event->getStart();
        $event->setExecutionTime($executionTime);

        $this->logs->enqueue($event);
    }

    /**
     * @return bool
     */
    public function hasLoggedEvents(): bool
    {
        return !$this->logs->isEmpty();
    }

    /**
     * @return LogEvent
     */
    public function getLoggedEvent(): LogEvent
    {
        if (!$this->hasLoggedEvents()) {
            throw new \LogicException('No more events logged!');
        }

        return $this->logs->dequeue();
    }
}
