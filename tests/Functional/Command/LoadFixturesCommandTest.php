<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Command\LoadFixturesCommand;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use MongoDB\Collection;
use MongoDB\Database;
use Symfony\Component\Console\Tester\CommandTester;

class LoadFixturesAppTest extends AppTestCase
{
    public function test_command()
    {
        /** @var Database $conn */
        $conn = $this->getContainer()->get('mongo.connection');
        self::assertEquals('testFunctionaldb', $conn->getDatabaseName());

        $conn->createCollection('testFixturesCollection');

        $this->getApplication()->add(new LoadFixturesCommand());

        $command = $this->getApplication()->find('mongodb:fixtures:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'addFixturesPath' => __DIR__ . "/../../fixtures/DataFixtures"
            ]
        );

        /** @var Collection $collection */
        $collection = $conn->selectCollection('testFixturesCollection');

        $fixtures = $collection->find(['type' => 'fixture']);
        $fixtures = $fixtures->toArray();

        self::assertEquals('fixture', $fixtures[0]['type']);
        self::assertEquals('test', $fixtures[0]['data']);

        self::assertContains("Done, loaded 4 fixtures files", $commandTester->getDisplay());

        $conn->dropCollection('testFixturesCollection');
    }

    public function test_command_not_fixtures_found()
    {
        /** @var Database $conn */
        $conn = $this->getContainer()->get('mongo.connection');
        self::assertEquals('testFunctionaldb', $conn->getDatabaseName());

        $this->getApplication()->add(new LoadFixturesCommand());

        $command = $this->getApplication()->find('mongodb:fixtures:load');

        $commandTester = new CommandTester($command);

        self::expectException(\InvalidArgumentException::class);
        $commandTester->execute([]);

        $conn->dropCollection('testFixturesCollection');
    }

    public function test_command_order_fixtures()
    {

        /** @var Database $conn */
        $conn = $this->getContainer()->get('mongo.connection');

        $conn->dropCollection('testFixturesOrderedCollection');

        self::assertEquals('testFunctionaldb', $conn->getDatabaseName());

        $conn->createCollection('testFixturesOrderedCollection');

        $this->getApplication()->add(new LoadFixturesCommand());

        $command = $this->getApplication()->find('mongodb:fixtures:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'addFixturesPath' => __DIR__ . "/../../fixtures/DataFixtures"
            ]
        );

        /** @var Collection $collection */
        $collection = $conn->selectCollection('testFixturesOrderedCollection');
        $fixtures = $collection->find(['type' => 'fixture']);
        $fixtures = $fixtures->toArray();

        self::assertEquals(3, count($fixtures));

        $fixture = current($fixtures);

        self::assertEquals('fixture', $fixture['type']);
        self::assertEquals('Batman Begins - 2005', $fixture['data']);
        self::assertEquals(0, $fixture['expectedPosition']);

        $fixture = next($fixtures);

        self::assertEquals('fixture', $fixture['type']);
        self::assertEquals('Edward Scissorhands - 1990', $fixture['data']);
        self::assertEquals(1, $fixture['expectedPosition']);

        $fixture = next($fixtures);

        self::assertEquals('fixture', $fixture['type']);
        self::assertEquals('Alice in Wonderland - 2010', $fixture['data']);
        self::assertEquals(2, $fixture['expectedPosition']);

        $conn->dropCollection('testFixturesOrderedCollection');
    }

}
