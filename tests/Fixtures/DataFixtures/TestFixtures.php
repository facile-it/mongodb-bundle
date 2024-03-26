<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Fixtures\DataFixtures;

use Facile\MongoDbBundle\Capsule\Database;
use Facile\MongoDbBundle\Fixtures\AbstractContainerAwareFixture;
use Facile\MongoDbBundle\Fixtures\MongoFixtureInterface;

class TestFixtures extends AbstractContainerAwareFixture implements MongoFixtureInterface
{
    public function loadData(): void
    {
        $doc = [
            'type' => 'fixture',
            'data' => 'test',
        ];

        /** @var Database $connection */
        $connection = $this->getContainer()->get('mongo.connection.test_db');
        $collection = $connection->selectCollection($this->collection());
        $collection->insertOne($doc);
    }

    /**
     * @return array
     */
    public function loadIndexes() {}

    public function collection(): string
    {
        return 'testFixturesCollection';
    }
}
