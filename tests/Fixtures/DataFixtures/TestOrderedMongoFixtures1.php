<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Fixtures\DataFixtures;

use Facile\MongoDbBundle\Capsule\Database;
use Facile\MongoDbBundle\Fixtures\AbstractContainerAwareFixture;
use Facile\MongoDbBundle\Fixtures\MongoFixtureInterface;
use Facile\MongoDbBundle\Fixtures\OrderedFixtureInterface;

class TestOrderedMongoFixtures1 extends AbstractContainerAwareFixture implements MongoFixtureInterface, OrderedFixtureInterface
{
    public function loadData()
    {
        $doc = [
            'type' => 'fixture',
            'data' => 'Alice in Wonderland - 2010',
            'expectedPosition' => 2,
        ];

        /** @var Database $connection */
        $connection = $this->getContainer()->get('mongo.connection.test_db');
        $collection = $connection->selectCollection($this->collection());
        $collection->insertOne($doc);
    }

    public function getOrder(): int
    {
        return 200;
    }

    public function loadIndexes()
    {
    }

    public function collection(): string
    {
        return 'testFixturesOrderedCollection';
    }
}
