<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use MongoDB\Database as Connection;

/**
 * Class AbstractCommand.
 */
abstract class AbstractCommand extends ContainerAwareCommand
{
    /** @var SymfonyStyle */
    protected $io;
    /** @var Connection */
    protected $connection;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection to use for this command')
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);
        $this->connection = $input->getOption('connection')
            ? $this->getContainer()->get('mongo.connection.'.$input->getOption('connection'))
            : $this->getContainer()->get('mongo.connection')
        ;
    }
}
