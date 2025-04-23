<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Loggers;

use Facile\MongoDbBundle\Models\Query;

class MongoQueryLogger implements DataCollectorLoggerInterface
{
    /** @var \SplQueue|Query[] */
    private readonly \SplQueue $logs;

    /** @var string[] */
    private array $connections = [];

    /**
     * MongoQueryLogger constructor.
     */
    public function __construct()
    {
        $this->logs = new \SplQueue();
    }

    public function addConnection(string $connection): void
    {
        $this->connections[] = $connection;
    }

    /**
     * @return string[]
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    public function logQuery(Query $event): void
    {
        $this->logs->enqueue($event);
    }

    public function getLoggedEvent(): Query
    {
        if (! $this->hasLoggedEvents()) {
            throw new \LogicException('No more events logged!');
        }

        return $this->logs->dequeue();
    }

    public function hasLoggedEvents(): bool
    {
        return ! $this->logs->isEmpty();
    }
}
