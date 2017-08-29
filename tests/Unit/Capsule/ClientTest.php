<?php

declare(strict_types=1);

use Facile\MongoDbBundle\Capsule\Client;
use MongoDB\Client as MongoClient;
use MongoDB\Database as MongoDatabase;
use MongoDB\Collection as MongoCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientTest extends TestCase
{
    public function test_mongodb_client_encapsulation()
    {
        $client = new Client('mongodb://localhost:27017', [], [], 'test_client', $this->prophesize(EventDispatcherInterface::class)->reveal());

        self::assertInstanceOf(MongoClient::class, $client);

        $database = $client->selectDatabase('test');
        self::assertInstanceOf(MongoDatabase::class, $database);

        $collection = $client->selectCollection('test', 'test_collection');
        self::assertInstanceOf(MongoCollection::class, $collection);
    }
}
