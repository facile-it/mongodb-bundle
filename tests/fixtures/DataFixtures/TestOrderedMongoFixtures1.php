<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\tests\fixtures\DataFixtures;

use Facile\MongoDbBundle\Capsule\Database;
use Facile\MongoDbBundle\Fixtures\AbstractContainerAwareFixture;
use Facile\MongoDbBundle\Fixtures\MongoFixtureInterface;
use Facile\MongoDbBundle\Fixtures\OrderedFixtureInterface;

class TestOrderedMongoFixtures1 extends AbstractContainerAwareFixture implements MongoFixtureInterface, OrderedFixtureInterface
{
    /**
     * @return array
     */
    public function loadData()
    {
        $doc = [
            'type' => 'fixture',
            'data' => 'Alice in Wonderland - 2010',
            'expectedPosition' => 2
        ];

        /** @var Database $connection */
        $connection = $this->getContainer()->get('mongo.connection.test_db');
        $collection = $connection->selectCollection($this->collection());
        $collection->insertOne($doc);
    }

    /**
     * Gets priority to sort fixtures order
     */
    public function getOrder()
    {
        return 200;
    }


    /**
     * @return array
     */
    public function loadIndexes()
    {
    }

    /**
     * @return string
     */
    public function collection(): string
    {
        return 'testFixturesOrderedCollection';
    }
}
