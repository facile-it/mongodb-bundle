<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Command\LoadFixturesCommand;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use MongoDB\Database;
use Symfony\Component\Console\Tester\CommandTester;

class LoadFixturesCommandTest extends AppTestCase
{
    /** @var Database */
    private $conn;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conn = $this->getContainer()->get('mongo.connection');
        self::assertEquals('testFunctionaldb', $this->conn->getDatabaseName());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->conn->dropCollection('testFixturesCollection');
        $this->conn->dropCollection('testFixturesOrderedCollection');
    }

    public function test_command(): void
    {
        $this->conn->createCollection('testFixturesCollection');

        $this->addCommandToApplication();
        $command = $this->getApplication()->find('mongodb:fixtures:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'addFixturesPath' => __DIR__ . '/../../Fixtures/DataFixtures',
            ]
        );

        $collection = $this->conn->selectCollection('testFixturesCollection');

        $fixtures = $collection->find(['type' => 'fixture'])
            ->toArray();

        self::assertEquals('fixture', $fixtures[0]['type']);
        self::assertEquals('test', $fixtures[0]['data']);

        self::assertStringContainsString('Done, loaded 4 fixtures files', $commandTester->getDisplay());
    }

    public function test_command_not_fixtures_found(): void
    {
        $this->addCommandToApplication();
        $command = $this->getApplication()->find('mongodb:fixtures:load');
        $commandTester = new CommandTester($command);

        $this->expectException(\InvalidArgumentException::class);
        $commandTester->execute([]);
    }

    public function test_command_order_fixtures(): void
    {
        $this->conn->createCollection('testFixturesOrderedCollection');

        $this->addCommandToApplication();
        $command = $this->getApplication()->find('mongodb:fixtures:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'addFixturesPath' => __DIR__ . '/../../Fixtures/DataFixtures',
            ]
        );

        $collection = $this->conn->selectCollection('testFixturesOrderedCollection');
        $fixtures = $collection
            ->find(['type' => 'fixture'])
            ->toArray();

        $this->assertCount(3, $fixtures);

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

        $this->conn->dropCollection('testFixturesOrderedCollection');
    }

    private function addCommandToApplication(): void
    {
        $container = $this->getApplication()
            ->getKernel()
            ->getContainer();

        $this->getApplication()->add(new LoadFixturesCommand($container));
    }
}
