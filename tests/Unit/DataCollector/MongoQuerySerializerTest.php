<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\DataCollector;

use Facile\MongoDbBundle\DataCollector\MongoQuerySerializer;
use Facile\MongoDbBundle\Models\Query;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;

class MongoQuerySerializerTest extends TestCase
{
    /**
     * @param $unserializedData
     *
     * @dataProvider unserializedDataProvider
     */
    public function test_serializer($unserializedData, $expectedSerialization)
    {
        $query = new Query();
        $query->setFilters($unserializedData);
        $query->setData($unserializedData);
        $query->setOptions($unserializedData);

        $clone = clone $query;
        MongoQuerySerializer::serialize($query);

        $this->assertNotEquals($clone->getFilters(), $query->getFilters());
        $this->assertNotEquals($clone->getData(), $query->getData());
        $this->assertNotEquals($clone->getOptions(), $query->getOptions());
        $this->assertEquals($expectedSerialization, $query->getFilters()['test']);
        $this->assertEquals($expectedSerialization, $query->getData()['test']);
        $this->assertEquals($expectedSerialization, $query->getOptions()['test']);
    }

    public function unserializedDataProvider()
    {
        $date = new UTCDateTime(1000);
        $dateTime = $date->toDateTime();
        $isoDate = sprintf('ISODate("%sT%s+00:00")', $dateTime->format('Y-m-d'), $dateTime->format('H:i:s'));

        // regression: string which is a FQCN of a class that has __toString
        $documentWithFQCN = new BSONDocument();
        $documentWithFQCN->fqcn = \Exception::class;

        return [
            [['test' => $date], $isoDate],
            [['test' => new BSONDocument()], []],
            [['test' => new \stdClass()], []],
            [['test' => $documentWithFQCN], ['fqcn' => \Exception::class]],
        ];
    }

    public function test_serializer_regression_with_replaceOne()
    {
        $stdClass = new \stdClass();
        $stdClass->one = 'one';
        $stdClass->two = 'two';
        $unserializedData = $this->prophesize(BSONDocument::class);
        $unserializedData->bsonSerialize()
            ->shouldBeCalled()
            ->willReturn($stdClass);

        $query = new Query();
        $query->setData($unserializedData->reveal());

        $clone = clone $query;
        MongoQuerySerializer::serialize($query);

        $this->assertNotEquals($clone->getData(), $query->getData());
        $this->assertEquals(['one' => 'one', 'two' => 'two'], $query->getData());
    }
}
