<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\fixtures\DataFixtures;

use Facile\MongoDbBundle\Capsule\Database;
use Facile\MongoDbBundle\Fixtures\AbstractContainerAwareFixture;
use Facile\MongoDbBundle\Fixtures\MongoFixtureInterface;
use Facile\MongoDbBundle\Fixtures\OrderedFixtureInterface;

class TestOrderedMongoFixtures extends AbstractContainerAwareFixture implements MongoFixtureInterface, OrderedFixtureInterface
{
    /**
     * @return array
     */
    public function loadData()
    {
        $doc = [
            'type' => 'fixture',
            'data' => 'Edward Scissorhands - 1990',
            'expectedPosition' => 1
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
        return 1;
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
