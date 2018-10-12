<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Loggers;

use Facile\MongoDbBundle\Models\Query;

/**
 * Class MongoQueryLogger
 */
class MongoQueryLogger implements DataCollectorLoggerInterface
{
    /** @var \SplQueue|Query[] */
    private $logs;
    /** @var array|string[] */
    private $connections;

    /**
     * MongoQueryLogger constructor.
     */
    public function __construct()
    {
        $this->logs = new \SplQueue();
        $this->connections = [];
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
     * @param Query $event
     */
    public function logQuery(Query $event)
    {
        $this->logs->enqueue($event);
    }

    /**
     * @return Query
     */
    public function getLoggedEvent(): Query
    {
        if (! $this->hasLoggedEvents()) {
            throw new \LogicException('No more events logged!');
        }

        return $this->logs->dequeue();
    }

    /**
     * @return bool
     */
    public function hasLoggedEvents(): bool
    {
        return ! $this->logs->isEmpty();
    }
}
