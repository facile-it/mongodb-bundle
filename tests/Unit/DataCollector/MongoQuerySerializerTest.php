<?php declare(strict_types=1);

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

        $clone = clone $query;
        MongoQuerySerializer::serialize($query);

        $this->assertNotEquals($clone->getFilters(), $query->getFilters());
        $this->assertEquals($expectedSerialization, $query->getFilters()['test']);
    }

    public function unserializedDataProvider()
    {
        $date = new UTCDateTime(1000);
        $dateTime = $date->toDateTime();
        $isoDate = sprintf("ISODate(\"%sT%s+00:00\")", $dateTime->format('Y-m-d'), $dateTime->format('H:i:s'));

        return [
                [['test' => $date], $isoDate],
                [['test' => new BSONDocument()], []],
                [['test' => new \stdClass()], []],
        ];
    }
}