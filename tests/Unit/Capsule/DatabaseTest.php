<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\Capsule;

use Prophecy\PhpUnit\ProphecyTrait;
use Facile\MongoDbBundle\Capsule\Collection;
use Facile\MongoDbBundle\Capsule\Database;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DatabaseTest extends TestCase
{
    use ProphecyTrait;

    public function test_selectCollection(): void
    {
        $manager = new Manager('mongodb://localhost');
        $logger = $this->prophesize(EventDispatcherInterface::class);

        $db = new Database($manager, 'client_name', 'testdb', [], $logger->reveal());
        self::assertInstanceOf(\MongoDB\Database::class, $db);

        $coll = $db->selectCollection('test_collection');

        self::assertInstanceOf(Collection::class, $coll);

        $debugInfo = $coll->__debugInfo();
        self::assertSame($manager, $debugInfo['manager']);
        self::assertEquals('testdb', $debugInfo['databaseName']);
    }

    public function test_withOptions(): void
    {
        $manager = new Manager('mongodb://localhost');
        $logger = $this->prophesize(EventDispatcherInterface::class);

        $db = new Database($manager, 'client_name', 'testdb', [], $logger->reveal());
        self::assertInstanceOf(\MongoDB\Database::class, $db);

        $newDb = $db->withOptions(['readPreference' => new ReadPreference(ReadPreference::RP_NEAREST)]);

        self::assertInstanceOf(Database::class, $newDb);

        $debugInfo = $newDb->__debugInfo();
        self::assertSame($manager, $debugInfo['manager']);
        self::assertEquals('testdb', $debugInfo['databaseName']);
        self::assertEquals(ReadPreference::RP_NEAREST, $debugInfo['readPreference']->getMode());
    }
}
