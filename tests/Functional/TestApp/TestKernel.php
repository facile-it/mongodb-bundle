<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\TestApp;

use Facile\MongoDbBundle\FacileMongoDbBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /**
     * @inheritDoc
     *
     * @return Bundle[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new MonologBundle(),
            new FacileMongoDbBundle(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config.yaml');

        if ('docker' === getenv('TEST_ENV')) {
            $loader->load(__DIR__ . '/docker.yaml');
        }

        if (version_compare(Kernel::VERSION, '6.1.0') >= 0) {
            $loader->load(__DIR__ . '/deprecations_6.1.yml');
        }

        if (version_compare(Kernel::VERSION, '6.4.0') >= 0) {
            $loader->load(__DIR__ . '/deprecations_6.4.yml');
        }
    }
}
