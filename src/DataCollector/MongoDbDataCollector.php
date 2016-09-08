<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\DataCollector;

use Facile\MongoDbBundle\Models\LogEvent;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class MongoDbDataCollector.
 */
final class MongoDbDataCollector extends DataCollector
{
    const QUERY_KEYWORD = 'queries';
    const CONNECTION_KEYWORD = 'connections';
    const TIME_KEYWORD = 'totalTime';

    /** @var DataCollectorLoggerInterface */
    private $logger;

    public function __construct()
    {
        $this->data = [
            self::QUERY_KEYWORD => [],
            self::TIME_KEYWORD => 0.0,
            self::CONNECTION_KEYWORD => [],
        ];
    }

    /**
     * @param DataCollectorLoggerInterface $logger
     */
    public function setLogger(DataCollectorLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        while ($this->logger->hasLoggedEvents()) {
            /** @var LogEvent $event */
            $event = $this->logger->getLoggedEvent();

            // with extension version under 1.2.0 some Mongo objects can't be automatically serialized
            if (-1 === version_compare(phpversion('mongodb'), '1.2.0')) {
                $event->setData($this->prepareUnserializableData($event->getData()));
                $event->setFilters($this->prepareUnserializableData($event->getFilters()));
            }

            $this->data[self::QUERY_KEYWORD][] = $event;
            $this->data[self::TIME_KEYWORD] += $event->getExecutionTime();
        }

        $this->data[self::CONNECTION_KEYWORD] = $this->logger->getConnections();
    }

    /**
     * @return int
     */
    public function getQueryCount(): int
    {
        return count($this->data[self::QUERY_KEYWORD]);
    }

    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->data[self::QUERY_KEYWORD];
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return (float)($this->data[self::TIME_KEYWORD] * 1000);
    }

    /**
     * @return int
     */
    public function getConnectionsCount(): int
    {
        return count($this->data[self::CONNECTION_KEYWORD]);
    }
    /**
     * @return array|string[]
     */
    public function getConnections(): array
    {
        return $this->data[self::CONNECTION_KEYWORD];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mongodb';
    }

    /**
     * @param array|object $data
     *
     * @return array|object
     */
    private function prepareUnserializableData($data)
    {
        foreach ($data as $key => $item) {
            if (method_exists($item, 'getArrayCopy')) {
                $data[$key] = $this->prepareUnserializableData($item->getArrayCopy());
            }

            if (method_exists($item, 'toDateTime')) {
                $data[$key] = $item->toDateTime()->format('r');
                continue;
            }

            if (method_exists($item, '__toString')) {
                $data[$key] = $item->__toString();
                continue;
            }

            if (is_array($item) || is_object($item)) {
                $data[$key] = $this->prepareUnserializableData((array)$item);
            }
        }

        return $data;
    }
}
