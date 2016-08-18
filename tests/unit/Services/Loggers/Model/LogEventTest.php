<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\unit\Services\Loggers\Model;

use Facile\MongoDbBundle\Services\Loggers\Model\LogEvent;
use MongoDB\BSON\UTCDatetime;
use MongoDB\Model\BSONDocument;

class LogEventTest extends \PHPUnit_Framework_TestCase
{
    public function test_construction()
    {
        $event = new LogEvent();
        $event->setCollection('test_collection');
        $event->setMethod('find');
        $event->setExecutionTime(1000);
        $event->setData(['_id'=>'1000000000001']);

        self::assertEquals('test_collection', $event->getCollection());
        self::assertEquals('find',$event->getMethod());
        self::assertEquals(1000,$event->getExecutionTime());
        self::assertEquals(['_id'=>'1000000000001'],$event->getData());
        self::assertEquals(json_encode(['_id'=>'1000000000001']),$event->getDataJson());
    }

    public function test_preSerialization()
    {
        $doc = new BSONDocument();
        $doc->data = new UTCDatetime(1471549143264);

        $event = new LogEvent();
        $event->setCollection('test_collection');
        $event->setMethod('find');
        $event->setExecutionTime(1000);
        $event->setData([$doc]);

        self::assertEquals('test_collection', $event->getCollection());
        self::assertEquals('find',$event->getMethod());
        self::assertEquals(1000,$event->getExecutionTime());
        self::assertEquals([['data'=>1471549143264]],$event->getData());
        self::assertEquals(json_encode([['data'=>"1471549143264"]]),$event->getDataJson());
    }
}
