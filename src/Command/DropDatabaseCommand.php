<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DropDatabaseCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('mongodb:database:drop')
            ->setDescription('Drops a database');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->writeln(sprintf('Dropping database %s', $this->connection->getDatabaseName()));
        $this->connection->drop();
        $this->io->writeln('Database dropped');

        return 0;
    }
}
