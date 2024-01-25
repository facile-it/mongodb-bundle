<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DropCollectionCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('mongodb:collection:drop')
            ->addArgument('collection', InputArgument::REQUIRED, 'collection to drop')
            ->setDescription('Drops a database');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collection = $input->getArgument('collection');

        $this->io->writeln(sprintf('Dropping collection %s', $collection));
        $this->connection->dropCollection($collection);
        $this->io->writeln('Collection dropped');

        return 0;
    }
}
