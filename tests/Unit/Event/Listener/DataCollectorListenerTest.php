<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Tests\Unit\Capsule;

use Facile\MongoDbBundle\Event\ConnectionEvent;
use Facile\MongoDbBundle\Event\Listener\DataCollectorListener;
use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Models\Query;
use Facile\MongoDbBundle\Services\Loggers\MongoLogger;

class DataCollectorListenerTest extends \PHPUnit_Framework_TestCase
{
    public function test_onConnectionClientCreated()
    {
        $event = new ConnectionEvent('test_client');

        $logger = new MongoLogger();
        $listener = new DataCollectorListener($logger);

        self::assertCount(0,$logger->getConnections());

        $listener->onConnectionClientCreated($event);

        self::assertCount(1,$logger->getConnections());
        self::assertContains('test_client', $logger->getConnections());
        self::assertFalse($logger->hasLoggedEvents());
    }

    public function test_onQueryExecuted()
    {
        $query = new Query();

        $event = new QueryEvent($query);

        $logger = new MongoLogger();
        $listener = new DataCollectorListener($logger);

        self::assertCount(0,$logger->getConnections());
        self::assertFalse($logger->hasLoggedEvents());

        $listener->onQueryExecuted($event);

        self::assertCount(0,$logger->getConnections());
        self::assertTrue($logger->hasLoggedEvents());

        self::assertSame($query, $logger->getLoggedEvent());
    }
}