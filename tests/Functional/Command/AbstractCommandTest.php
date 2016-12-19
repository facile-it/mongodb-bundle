<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Command\AbstractCommand;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class AbstractAppTest extends AppTestCase
{
    /**
     * @dataProvider commandOptionsProvider
     */
    public function test_AbstractCommand_execution(array $arguments)
    {

        $this->getApplication()->add(new FakeCommand());

        $command = $this->getApplication()->find('mongodb:fake:command');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(['command' => $command->getName()], $arguments));

        self:self::assertContains('Executed', $commandTester->getDisplay());
    }

    public function test_AbstractCommand_connection_exception()
    {

        $this->getApplication()->add(new FakeCommand());

        $command = $this->getApplication()->find('mongodb:fake:command');

        $unexistantConnectionName = 'unexistant_connection';

        $commandTester = new CommandTester($command);

        self::expectException(\LogicException::class);
        self::expectExceptionMessage(sprintf('No connection named \'%s\' found', $unexistantConnectionName));
        $commandTester->execute(['command' => $command->getName(), '--connection' => $unexistantConnectionName]);
    }


    public function commandOptionsProvider()
    {

        return [
            [[]],
            [['--connection' => 'test_db']],
        ];
    }
}


class FakeCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('mongodb:fake:command')
            ->setDescription('fake test command');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->writeln('Executed');
    }
}