<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Capsule;

use Facile\MongoDbBundle\Capsule\Collection;
use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use MongoDB\Driver\Manager;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

class CollectionTest extends AppTestCase
{
    private function getManager(): Manager
    {
        /** @var \Facile\MongoDbBundle\Services\ClientRegistry $reg */
        $reg = $this->getContainer()->get('mongo.client_registry');
        /** @var \MongoDB\Client $client */
        $client = $reg->getClient('test_client', 'testdb');

        return $client->__debugInfo()['manager'];
    }

    public function test_construction()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(0);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        self::assertInstanceOf(\MongoDB\Collection::class, $coll);
    }

    public function test_insertOne()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->insertOne(['test' => 1]);
    }

    public function test_updateOne()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->updateOne(['filter' => 1], ['$set' => ['testField' => 1]]);
    }

    public function test_count()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->count(['test' => 1]);
    }

    public function test_find()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->find([]);
    }

    public function test_findOne()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->findOne([]);
    }

    public function test_findOneAndUpdate()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->findOneAndUpdate([], ['$set' => ['country' => 'us']]);
    }

    public function test_findOneAndDelete()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->findOneAndDelete([]);
    }

    public function test_deleteOne()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->deleteOne([]);
    }

    public function test_replaceOne()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->replaceOne([], []);
    }

    public function test_aggregate()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(5);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->deleteMany([]);

        $coll->insertOne(['group' => 'a', 'testValue' => 2]);
        $coll->insertOne(['group' => 'a', 'testValue' => 3]);
        $coll->insertOne(['group' => 'b', 'testValue' => 2]);

        $result = $coll->aggregate([
            ['$match' => ['group' => 'a']],
            ['$group' => ['_id' => '$group', 'value' => ['$sum' => '$testValue']]],
        ]);

        $results = [];
        foreach ($result as $res) {
            $res = (array) $res;
            $results[] = ['group' => $res['_id'], 'value' => $res['value']];
        }

        self::assertCount(1, $results);
        self::assertEquals('a', $results[0]['group']);
        self::assertEquals(5, $results[0]['value']);
    }

    /** leave this test as last one to clean the collection*/
    public function test_deleteMany()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->deleteMany([]);
    }

    public function test_distinct()
    {
        $manager = $this->getManager();
        $ev = $this->mockEventDispatcher(1);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev);

        $coll->distinct('field');
    }

    private function mockEventDispatcher(int $times): EventDispatcherInterface
    {
        $ed = $this->prophesize(EventDispatcherInterface::class);

        if (! class_exists(\Symfony\Component\EventDispatcher\Event::class) || class_exists(LegacyEventDispatcherProxy::class)) {
            $ed->dispatch(Argument::type(QueryEvent::class), QueryEvent::QUERY_PREPARED)
                ->shouldBeCalledTimes($times)
                ->willReturnArgument(0);
            $ed->dispatch(Argument::type(QueryEvent::class), QueryEvent::QUERY_EXECUTED)
                ->shouldBeCalledTimes($times)
                ->willReturnArgument(0);
        } else {
            $ed->dispatch(QueryEvent::QUERY_PREPARED, Argument::type(QueryEvent::class))
                ->shouldBeCalledTimes($times);
            $ed->dispatch(QueryEvent::QUERY_EXECUTED, Argument::type(QueryEvent::class))
                ->shouldBeCalledTimes($times);
        }

        if (class_exists(\Symfony\Component\EventDispatcher\Event::class) && class_exists(LegacyEventDispatcherProxy::class)) {
            return LegacyEventDispatcherProxy::decorate($ed->reveal());
        }

        return $ed->reveal();
    }
}
