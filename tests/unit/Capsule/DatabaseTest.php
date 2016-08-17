<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Tests\unit\Capsule;

use Facile\MongoDbBundle\Capsule\Collection;
use Facile\MongoDbBundle\Capsule\Database;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use MongoDB\Driver\Manager;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    public function test_selectCollection()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);

        $db = new Database($manager, 'testdb', [], $logger->reveal());
        self::assertInstanceOf(\MongoDB\Database::class, $db);

        $coll = $db->selectCollection('test_collection');

        self::assertInstanceOf(Collection::class,$coll);

        $debugInfo = $coll->__debugInfo();
        self::assertSame($manager, $debugInfo['manager']);
        self::assertEquals('testdb', $debugInfo['databaseName']);
    }
}