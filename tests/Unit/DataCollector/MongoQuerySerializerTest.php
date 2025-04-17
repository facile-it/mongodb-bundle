<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\DataCollector;

use Prophecy\PhpUnit\ProphecyTrait;
use Facile\MongoDbBundle\DataCollector\MongoQuerySerializer;
use Facile\MongoDbBundle\Models\Query;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;

class MongoQuerySerializerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @dataProvider unserializedDataProvider
     */
    public function test_serializer(array $unserializedData, string|int|array $expectedSerialization): void
    {
        $query = new Query();
        $query->setFilters($unserializedData);
        $query->setData($unserializedData);
        $query->setOptions($unserializedData);

        MongoQuerySerializer::serialize($query);

        $this->assertEquals($expectedSerialization, $query->getFilters()['test']);
        $this->assertEquals($expectedSerialization, $query->getData()['test']);
        $this->assertEquals($expectedSerialization, $query->getOptions()['test']);
    }

    public function unserializedDataProvider(): array
    {
        $date = new UTCDateTime(1_000);
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
            [['test' => 'stringValue'], 'stringValue'],
            [['test' => 145], 145],
        ];
    }

    public function test_serializer_terminating(): void
    {
        // tests that the serializer terminates when serializing an object which references itself
        $selfReferencingObject = new class () {
            public self $self;

            public function __construct(
            ) {
                $this->self = $this;
            }
        };
        $data = ['test' => $selfReferencingObject];

        $query = new Query();
        $query->setFilters($data);
        $query->setData($data);
        $query->setOptions($data);

        MongoQuerySerializer::serialize($query);

        $this->expectNotToPerformAssertions();
    }

    public function test_serializer_regression_with_replaceOne(): void
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
