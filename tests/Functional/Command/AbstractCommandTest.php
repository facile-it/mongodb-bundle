<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Command\AbstractCommand;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class AbstractCommandTest extends AppTestCase
{
    /**
     * @dataProvider commandOptionsProvider
     */
    public function test_AbstractCommand_execution(array $arguments): void
    {
        $this->addCommandToApplication();

        $command = $this->getApplication()->find('mongodb:fake:command');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(['command' => $command->getName()], $arguments));

        self::assertStringContainsString('Executed', $commandTester->getDisplay());
    }

    public function test_AbstractCommand_connection_exception(): void
    {
        $this->addCommandToApplication();

        $command = $this->getApplication()->find('mongodb:fake:command');

        $unexistantConnectionName = 'unexistant_connection';

        $commandTester = new CommandTester($command);

        self::expectException(\LogicException::class);
        self::expectExceptionMessage(sprintf('No connection named \'%s\' found', $unexistantConnectionName));
        $commandTester->execute(['command' => $command->getName(), '--connection' => $unexistantConnectionName]);
    }

    public function commandOptionsProvider(): array
    {
        return [
            [[]],
            [['--connection' => 'test_db']],
        ];
    }

    private function addCommandToApplication(): void
    {
        $container = $this->getApplication()
            ->getKernel()
            ->getContainer();

        $this->getApplication()->add(new FakeCommand($container));
    }
}

class FakeCommand extends AbstractCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('mongodb:fake:command')
            ->setDescription('fake test command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->writeln('Executed');

        return 0;
    }
}
