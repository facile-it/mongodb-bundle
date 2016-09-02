<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Command\DropCollectionCommand;
use MongoDB\Database;
use Symfony\Component\Console\Tester\CommandTester;

class DropCollectionCommandTest extends CommandTestCase
{
    public function test_command()
    {
        /** @var Database $conn */
        $conn = $this->getContainer()->get('mongo.connection');
        self::assertEquals('testFunctionaldb', $conn->getDatabaseName());

        $conn->dropCollection('testFunctionalCollection');
        $conn->createCollection('testFunctionalCollection');

        $this->getApplication()->add(new DropCollectionCommand());

        $command = $this->getApplication()->find('mongo:collection:drop');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'collection' => 'testFunctionalCollection']);

        self:self::assertContains('Collection dropped', $commandTester->getDisplay());
    }

}