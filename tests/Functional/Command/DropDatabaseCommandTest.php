<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Command\DropDatabaseCommand;
use MongoDB\Database;
use Symfony\Component\Console\Tester\CommandTester;

class DropDatabaseCommandTest extends CommandTestCase
{
    public function test_command()
    {
        /** @var Database $conn */
        $conn = $this->getContainer()->get('mongo.connection');
        self::assertEquals('testFunctionaldb', $conn->getDatabaseName());

        $conn->createCollection('testFunctionalCollection');

        $this->getApplication()->add(new DropDatabaseCommand());

        $command = $this->getApplication()->find('mongo:database:drop');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        self:self::assertContains('Database dropped', $commandTester->getDisplay());
    }
}