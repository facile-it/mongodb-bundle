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

    public function test_insertOne()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->insertOne(['test' => 1]);
    }

    public function test_updateOne()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->updateOne(['filter' => 1],['$set' => ['testField' => 1]]);
    }

    public function test_count()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->count(['test' => 1]);
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

    public function test_log_event_instantiation()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = new FakeLogger();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger);

        $coll->replaceOne(['filter' => 1], ['replace' => 1], ['option' => true]);

        self::assertTrue($logger->hasLoggedEvents());
        $event = $logger->getLoggedEvent();
        self::assertFalse($logger->hasLoggedEvents());

        self::assertEquals('test_collection',$event->getCollection());
        self::assertNotEmpty($event->getExecutionTime());
        self::assertEquals(['filter' => 1], $event->getFilters());
        self::assertEquals(['replace' => 1],$event->getData());
        self::assertEquals(['option' => true],$event->getOptions());

    }

    public function test_aggregate()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->deleteMany([]);

        $coll->insertOne(['group' => 'a', 'testValue' => 2]);
        $coll->insertOne(['group' => 'a', 'testValue' => 3]);
        $coll->insertOne(['group' => 'b', 'testValue' => 2]);

        $result = $coll->aggregate([
            ['$match' => ['group' => 'a']],
            ['$group' => ['_id' => '$group', 'value' => ['$sum'=>'$testValue']]]
        ]);

        $results = [];
        foreach ($result as $res) {
            $results[] = ['group' => $res['_id'], 'value' => $res['value']];
        }

        self::assertCount(1, $results);
        self::assertEquals('a', $results[0]['group']);
        self::assertEquals(5, $results[0]['value']);
    }

    /** leave this test as last one to clean the collection*/
    public function test_deleteMany()
    {
        $manager = new Manager('mongodb://localhost');
        $logger = self::prophesize(DataCollectorLoggerInterface::class);
        $logger->startLogging(Argument::type(LogEvent::class))->shouldBeCalled();
        $logger->logQuery(Argument::type(LogEvent::class))->shouldBeCalled();

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $logger->reveal());

        $coll->deleteMany([]);
    }
}

class FakeLogger extends \Facile\MongoDbBundle\Services\Loggers\MongoLogger
{
}
