<?php

declare(strict_types=1);

use Facile\MongoDbBundle\Capsule\Collection;
use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use MongoDB\Driver\Manager;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CollectionTest extends AppTestCase
{
    public function test_construction()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        self::assertInstanceOf(\MongoDB\Collection::class, $coll);
    }

    public function test_insertOne()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->insertOne(['test' => 1]);
    }

    public function test_updateOne()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->updateOne(['filter' => 1],['$set' => ['testField' => 1]]);
    }

    public function test_count()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->count(['test' => 1]);
    }

    public function test_find()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->find([]);
    }

    public function test_findOne()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->findOne([]);
    }

    public function test_findOneAndUpdate()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->findOneAndUpdate([], ['$set' => ['country' => 'us']]);
    }

    public function test_findOneAndDelete()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->findOneAndDelete([]);
    }

    public function test_deleteOne()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->deleteOne([]);
    }

    public function test_replaceOne()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->replaceOne([], []);
    }

    public function test_aggregate()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

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
            $res = (array)$res;
            $results[] = ['group' => $res['_id'], 'value' => $res['value']];
        }

        self::assertCount(1, $results);
        self::assertEquals('a', $results[0]['group']);
        self::assertEquals(5, $results[0]['value']);
    }

    /** leave this test as last one to clean the collection*/
    public function test_deleteMany()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->deleteMany([]);
    }

    public function test_distinct()
    {
        $manager = new Manager('mongodb://localhost:27017');
        $ev = self::prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'testdb', 'test_collection', [], $ev->reveal());

        $coll->distinct('field');
    }

    /**
     * @param $ev
     */
    protected function assertEventsDispatching($ev)
    {
        $ev->dispatch(QueryEvent::QUERY_PREPARED, Argument::type(QueryEvent::class))->shouldBeCalled();
        $ev->dispatch(QueryEvent::QUERY_EXECUTED, Argument::type(QueryEvent::class))->shouldBeCalled();
    }
}
