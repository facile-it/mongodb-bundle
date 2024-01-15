<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Command;

use MongoDB\Database as Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractCommand.
 */
abstract class AbstractCommand extends Command
{
    /** @var SymfonyStyle */
    protected $io;

    /** @var Connection */
    protected $connection;

    /** @var ContainerInterface */
    private $container;

    /**
     * AbstractCommand constructor.
     */
    public function __construct(ContainerInterface $container, string $name = null)
    {
        parent::__construct($name);
        $this->container = $container;
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection to use for this command');
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);

        $connectionName = 'mongo.connection';

        if ($input->getOption('connection')) {
            $connectionName .= '.' . $input->getOption('connection');
        }

        if (! $this->container->has($connectionName)) {
            throw new \LogicException(sprintf('No connection named \'%s\' found', $input->getOption('connection')));
        }

        $this->connection = $this->container->get($connectionName);
    }
}
