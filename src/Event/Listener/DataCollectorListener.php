<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Event\Listener;

use Facile\MongoDbBundle\Event\ConnectionEvent;
use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;

/**
 * Class DataCollectorListener.
 * @internal
 */
final class DataCollectorListener
{
    /** @var DataCollectorLoggerInterface */
    private $logger;

    /**
     * DataCollectorListener constructor.
     *
     * @param DataCollectorLoggerInterface $logger
     */
    public function __construct(DataCollectorLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ConnectionEvent $event
     */
    public function onConnectionClientCreated(ConnectionEvent $event)
    {
        $this->logger->addConnection($event->getClientName());
    }

    /**
     * @param QueryEvent $event
     */
    public function onQueryExecuted(QueryEvent $event)
    {
        $this->logger->logQuery($event->getQueryLog());
    }
}
