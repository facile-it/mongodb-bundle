<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\DataCollector;

use Facile\MongoDbBundle\Models\Query;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class MongoDbDataCollector.
 *
 * @internal
 */
class MongoDbDataCollector extends DataCollector
{
    public const QUERY_KEYWORD = 'queries';

    public const CONNECTION_KEYWORD = 'connections';

    public const TIME_KEYWORD = 'totalTime';

    /** @var DataCollectorLoggerInterface */
    private $logger;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->data = [
            self::QUERY_KEYWORD => [],
            self::TIME_KEYWORD => 0.0,
            self::CONNECTION_KEYWORD => [],
        ];
    }

    public function setLogger(DataCollectorLoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function collect(Request $request, Response $response, $exception = null): void
    {
        if ($exception && ! $exception instanceof \Throwable) {
            throw new \InvalidArgumentException('Expecting \Throwable, got ' . get_debug_type($exception));
        }

        while ($this->logger->hasLoggedEvents()) {
            /** @var Query $event */
            $event = $this->logger->getLoggedEvent();

            MongoQuerySerializer::serialize($event);

            $this->data[self::QUERY_KEYWORD][] = $event;
            $this->data[self::TIME_KEYWORD] += $event->getExecutionTime();
        }

        $this->data[self::CONNECTION_KEYWORD] = $this->logger->getConnections();
    }

    public function getQueryCount(): int
    {
        return \count($this->data[self::QUERY_KEYWORD]);
    }

    /**
     * @return Query[]|array
     */
    public function getQueries(): array
    {
        return $this->data[self::QUERY_KEYWORD];
    }

    public function getTime(): float
    {
        return (float) ($this->data[self::TIME_KEYWORD] * 1_000);
    }

    public function getConnectionsCount(): int
    {
        return \count($this->data[self::CONNECTION_KEYWORD]);
    }

    /**
     * @return array|string[]
     */
    public function getConnections(): array
    {
        return $this->data[self::CONNECTION_KEYWORD];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'mongodb';
    }
}
