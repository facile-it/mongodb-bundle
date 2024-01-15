<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Event\Listener;

use Facile\MongoDbBundle\Event\ConnectionEvent;
use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;

/**
 * Class DataCollectorListener.
 *
 * @internal
 */
final class DataCollectorListener
{
    /** @var DataCollectorLoggerInterface */
    private $logger;

    public function __construct(DataCollectorLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onConnectionClientCreated(ConnectionEvent $event)
    {
        $this->logger->addConnection($event->getClientName());
    }

    public function onQueryExecuted(QueryEvent $event)
    {
        $this->logger->logQuery($event->getQueryLog());
    }
}
