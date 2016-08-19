<?php

declare(strict_types=1);

use Facile\MongoDbBundle\Capsule\Collection;
use Facile\MongoDbBundle\Models\LogEvent;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use MongoDB\Driver\Manager;
use Prophecy\Argument;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function test_construction()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        self::assertInstanceOf(\MongoDB\Collection::class, $coll);
    }

    public function test_find()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->find([]);
    }

    public function test_findOne()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->findOne([]);
    }

    public function test_findOneAndUpdate()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->findOneAndUpdate([], ['$set' => ['country' => 'us']]);
    }

    public function test_findOneAndDelete()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->findOneAndDelete([]);
    }

    public function test_insertOne()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->insertOne([]);
    }

    public function test_deleteMany()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->deleteMany([]);
    }

    public function test_deleteOne()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->deleteOne([]);
    }

    public function test_replaceOne()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->replaceOne([], []);
    }
}
