<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Tests\unit\DataCollector;

use Facile\MongoDbBundle\DataCollector\MongoDbDataCollector;
use Facile\MongoDbBundle\Services\Loggers\Model\LogEvent;
use Facile\MongoDbBundle\Services\Loggers\MongoLogger;
use Facile\MongoDbBundle\Services\Loggers\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class MongoDbDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function test_construction_null_logger()
    {
        $collector = new MongoDbDataCollector();
        $collector->setLogger(new NullLogger());
        $collector->collect(new Request(), new Response());

        self::assertEquals(0, $collector->getQueryCount());
        self::assertEmpty($collector->getQueries());

        self::assertEquals(0, $collector->getTime());

        self::assertEmpty($collector->getConnections());
        self::assertEquals(0, $collector->getConnectionsCount());

        self::assertEquals('mongodb', $collector->getName());
    }

    public function test_construction_logger()
    {
        $logger = new MongoLogger();

        $collector = new MongoDbDataCollector();
        $collector->setLogger($logger);
        $logger->logQuery(new LogEvent());
        $logger->addConnection('test_conenction');

        $collector->collect(new Request(), new Response());

        self::assertEquals(1, $collector->getQueryCount());
        self::assertNotEmpty($collector->getQueries());

        self::assertTrue(is_float($collector->getTime()));

        self::assertNotEmpty($collector->getConnections());
        self::assertEquals(1, $collector->getConnectionsCount());

        self::assertEquals('mongodb', $collector->getName());
    }
}
