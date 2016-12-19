<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Command\LoadFixturesCommand;
use MongoDB\Collection;
use MongoDB\Database;
use Symfony\Component\Console\Tester\CommandTester;

class LoadFixturesCommandTest extends CommandTestCase
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

        self::assertCount(1, $fixtures);
        self::assertEquals('fixture', $fixtures[0]['type']);
        self::assertEquals('test', $fixtures[0]['data']);

        $conn->dropCollection('testFixturesCollection');
    }
}