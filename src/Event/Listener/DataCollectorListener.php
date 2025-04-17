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
    public function __construct(private readonly DataCollectorLoggerInterface $logger)
    {
    }

    public function onConnectionClientCreated(ConnectionEvent $event): void
    {
        $this->logger->addConnection($event->getClientName());
    }

    public function onQueryExecuted(QueryEvent $event): void
    {
        $this->logger->logQuery($event->getQueryLog());
    }
}
