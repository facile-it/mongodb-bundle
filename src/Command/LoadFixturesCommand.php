<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Command;

use Facile\MongoDbBundle\Fixtures\FixtureSorter;
use Facile\MongoDbBundle\Fixtures\MongoFixtureInterface;
use Facile\MongoDbBundle\Fixtures\MongoFixturesLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class LoadFixturesCommand.
 */
class LoadFixturesCommand extends AbstractCommand
{
    /** @var MongoFixturesLoader */
    private $loader;

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('mongodb:fixtures:load')
            ->addArgument('addFixturesPath', InputArgument::OPTIONAL, 'Add a path to search in for fixtures files')
            ->setDescription('Load fixtures and applies them');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->loader = new MongoFixturesLoader($this->getContainer());
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->writeln('Loading mongo fixtures');
        /** @var Application $application */
        $application = $this->getApplication();

        $paths = $this->prepareSearchPaths($input, $application->getKernel());

        $this->loadPaths($paths);

        $fixtures = $this->loader->getLoadedClasses();
        if (empty($fixtures)) {
            throw new \InvalidArgumentException(
                sprintf('Could not find any class to load in: %s', "\n\n- " . implode("\n- ", $paths))
            );
        }

        foreach (FixtureSorter::sort($fixtures) as $fixture) {
            $this->loadFixture($fixture);
        }

        $this->io->writeln(sprintf('Done, loaded %d fixtures files', \count($fixtures)));

        return 0;
    }

    private function loadFixture(MongoFixtureInterface $indexList): void
    {
        $indexList->loadData();
        $indexList->loadIndexes();
        $this->io->writeln('Loaded fixture: ' . \get_class($indexList));
    }

    private function prepareSearchPaths(InputInterface $input, KernelInterface $kernel): array
    {
        $paths = [];

        if ($input->getArgument('addFixturesPath')) {
            $paths[] = $input->getArgument('addFixturesPath');
        }

        foreach ($kernel->getBundles() as $bundle) {
            $paths[] = $bundle->getPath() . '/DataFixtures/Mongo';
        }

        return $paths;
    }

    private function loadPaths(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $this->loader->loadFromDirectory($path);
            }
            if (is_file($path)) {
                $this->loader->loadFromFile($path);
            }
        }
    }
}
