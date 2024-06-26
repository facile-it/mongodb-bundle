<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Command\DropDatabaseCommand;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use MongoDB\Database;
use Symfony\Component\Console\Tester\CommandTester;

class DropDatabaseCommandTest extends AppTestCase
{
    public function test_command(): void
    {
        /** @var Database $conn */
        $conn = $this->getContainer()->get('mongo.connection');
        self::assertEquals('testFunctionaldb', $conn->getDatabaseName());

        $conn->createCollection('testFunctionalCollection');

        $this->addCommandToApplication();

        $command = $this->getApplication()->find('mongo:database:drop');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        self::assertStringContainsString('Database dropped', $commandTester->getDisplay());
    }

    private function addCommandToApplication(): void
    {
        $container = $this->getApplication()
            ->getKernel()
            ->getContainer();

        $this->getApplication()->add(new DropDatabaseCommand($container));
    }
}
