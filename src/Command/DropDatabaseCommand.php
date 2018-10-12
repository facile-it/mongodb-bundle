<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DropDatabaseCommand.
 */
class DropDatabaseCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('mongodb:database:drop')
            ->setDescription('Drops a database');;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->writeln(sprintf('Dropping database %s', $this->connection->getDatabaseName()));
        $this->connection->drop();
        $this->io->writeln('Database dropped');
    }
}
