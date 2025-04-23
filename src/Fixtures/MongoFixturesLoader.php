<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

use Symfony\Component\DependencyInjection\ContainerInterface;

final class MongoFixturesLoader
{
    /** @var array|MongoFixtureInterface[] */
    private ?array $loadedClasses = null;

    public function __construct(private readonly ContainerInterface $container) {}

    /**
     * @return array
     */
    public function loadFromDirectory(string $dir)
    {
        if (! is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exist', $dir));
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        return $this->loadFromIterator($iterator);
    }

    /**
     * @return array
     */
    private function loadFromIterator(\Iterator $iterator): mixed
    {
        $includedFiles = [];
        foreach ($iterator as $file) {
            if ($file->getBasename('.php') == $file->getBasename()) {
                continue;
            }
            $sourceFile = realpath($file->getPathName());
            require_once $sourceFile;
            $includedFiles[] = $sourceFile;
        }

        $declared = get_declared_classes();

        return array_reduce(
            $declared,
            function ($classList, string $className) use ($includedFiles) {
                /** @var class-string $className */
                $reflClass = new \ReflectionClass($className);
                $sourceFile = $reflClass->getFileName();

                if (
                    \in_array($sourceFile, $includedFiles)
                    && \array_key_exists(MongoFixtureInterface::class, $reflClass->getInterfaces())
                ) {
                    $instance = $this->buildFixture(new $className());
                    $this->addInstance($instance);
                    $classList[] = $instance;
                }

                return $classList;
            },
            []
        );
    }

    private function buildFixture(object $instance): MongoFixtureInterface
    {
        if ($instance instanceof AbstractContainerAwareFixture) {
            $instance->setContainer($this->container);
        }

        return $instance;
    }

    public function addInstance(MongoFixtureInterface $list): void
    {
        $listClass = $list::class;

        if (! isset($this->loadedClasses[$listClass])) {
            $this->loadedClasses[$listClass] = $list;
        }
    }

    /**
     * @return array
     */
    public function loadFromFile(string $fileName)
    {
        if (! is_readable($fileName)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exist or is not readable', $fileName));
        }

        $iterator = new \ArrayIterator([new \SplFileInfo($fileName)]);

        return $this->loadFromIterator($iterator);
    }

    /**
     * @return array|MongoFixtureInterface[]
     */
    public function getLoadedClasses(): ?array
    {
        return $this->loadedClasses;
    }
}
