<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\unit\Services\Loggers;

use Facile\MongoDbBundle\Models\Query;
use Facile\MongoDbBundle\Services\Loggers\MongoLogger;

/**
 * Class MongoLoggerTest.
 */
class MongoLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function test_logger_connections()
    {
        $logger = new MongoLogger();

        $logger->addConnection('test_connection');
        $logger->addConnection('test_connection2');

        self::assertEquals(['test_connection', 'test_connection2'], $logger->getConnections());

    }

    public function test_logger_queries()
    {
        $event1 = new Query();
        $event1->setCollection('coll1');
        $event2 = new Query();
        $event2->setCollection('coll2');

        $logger = new MongoLogger();
        self::assertFalse($logger->hasLoggedEvents());

        $logger->logQuery($event1);
        self::assertTrue($logger->hasLoggedEvents());

        $logger->logQuery($event2);
        self::assertTrue($logger->hasLoggedEvents());

        $e1 = $logger->getLoggedEvent();
        self::assertSame($event1, $e1);

        $e2 = $logger->getLoggedEvent();
        self::assertSame($event2, $e2);

        self::assertFalse($logger->hasLoggedEvents());
        self::expectException(\LogicException::class);
        $logger->getLoggedEvent();
    }
}
