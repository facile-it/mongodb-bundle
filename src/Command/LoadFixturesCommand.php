<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Command;

use Facile\MongoDbBundle\Fixtures\MongoFixturesLoader;
use Facile\MongoDbBundle\Fixtures\MongoFixtureInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoadFixturesCommand.
 */
class LoadFixturesCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('mongodb:fixtures:load')
            ->setDescription('Load fixtures and applies them');
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->writeln('Loading mongo fixtures');
        /** @var Application $application */
        $application = $this->getApplication();

        $paths = array();
        foreach ($application->getKernel()->getBundles() as $bundle) {
            $paths[] = $bundle->getPath().'/DataFixtures/Mongo';
        }

        $loader = new MongoFixturesLoader($this->getContainer());

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
            if (is_file($path)) {
                $loader->loadFromFile($path);
            }
        }

        $fixtures = $loader->getLoadedClasses();
        if (empty($fixtures)) {
            throw new \InvalidArgumentException(
                sprintf('Could not find any class to load in: %s', "\n\n- ".implode("\n- ", $paths))
            );
        }

        foreach ($fixtures as $fixture){
            $this->loadFixture($fixture);
        }

        $this->io->writeln('Done.');
    }

    /**
     * @param MongoFixtureInterface $indexList
     */
    private function loadFixture(MongoFixtureInterface $indexList)
    {
        $indexList->loadData();
        $indexList->loadIndexes();
        $this->io->writeln('Loaded fixture: '. get_class($indexList));
    }
}
