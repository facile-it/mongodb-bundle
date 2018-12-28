<?php declare(strict_types=1);

namespace App\Command;

use Facile\MongoDbBundle\Capsule\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadSampleDBCommand extends Command
{
    /** @var Database */
    private $database;

    protected static $defaultName = 'sampledb:load';

    public function __construct(Database $database)
    {
        parent::__construct();

        $this->database = $database;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Loading db');
    }
}
