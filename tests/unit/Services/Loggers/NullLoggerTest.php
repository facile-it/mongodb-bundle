<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests;

use Facile\MongoDbBundle\Services\Loggers\Model\LogEvent;
use Facile\MongoDbBundle\Services\Loggers\NullLogger;

/**
 * Class NullLoggerTest.
 */
class NullLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function test_logger()
    {
        $logger = new NullLogger();
        self::assertEquals(null,$logger->startLogging(new LogEvent()));

        self::assertEquals(null,$logger->addConnection('test'));
        self::assertEquals([],$logger->getConnections());

        self::assertEquals(null,$logger->logQuery(new LogEvent()));
        self::assertFalse($logger->hasLoggedEvents());

        self::expectException(\LogicException::class);
        $logger->getLoggedEvent();
    }
}
