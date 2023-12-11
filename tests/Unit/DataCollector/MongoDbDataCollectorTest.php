<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\DataCollector;

use Facile\MongoDbBundle\DataCollector\MongoDbDataCollector;
use Facile\MongoDbBundle\Models\Query;
use Facile\MongoDbBundle\Services\Loggers\MongoQueryLogger;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MongoDbDataCollectorTest extends TestCase
{
    public function test_construction_logger(): void
    {
        $logEvent = new Query();
        $logEvent->setData(
            [
                'data' => new BSONDocument(['test' => 'test']),
                'date' => $this->getUtcDateTime(),
            ]
        );

        $logger = new MongoQueryLogger();
        $logger->logQuery($logEvent);
        $logger->addConnection('test_connection');

        $collector = new MongoDbDataCollector();
        $collector->setLogger($logger);

        $collector->collect(new Request(), new Response());

        self::assertEquals(1, $collector->getQueryCount());
        self::assertNotEmpty($collector->getQueries());

        self::assertIsFloat($collector->getTime());

        self::assertNotEmpty($collector->getConnections());
        self::assertEquals(1, $collector->getConnectionsCount());

        self::assertEquals('mongodb', $collector->getName());
    }

    public function getUtcDateTime(): UTCDateTime
    {
        if (phpversion('mongodb') === '1.2.0-dev') {
            return new UTCDateTime('1000');
        }

        return new UTCDateTime(1000);
    }
}
