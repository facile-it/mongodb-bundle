<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DropCollectionCommand.
 */
class DropCollectionCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('mongodb:collection:drop')
            ->addArgument('collection', InputArgument::REQUIRED, 'collection to drop')
            ->setDescription('Drops a database');;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = $input->getArgument('collection');

        $this->io->writeln(sprintf('Dropping collection %s', $collection));
        $this->connection->dropCollection($collection);
        $this->io->writeln('Collection dropped');
    }
}
