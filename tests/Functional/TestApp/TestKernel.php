<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\TestApp;

use Facile\MongoDbBundle\FacileMongoDbBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class TestKernel.
 */
class TestKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new FacileMongoDbBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (version_compare(Kernel::VERSION, '3.2.0') === -1) {
            $loader->load(__DIR__.'/config_test.yml');
        }

        if (version_compare(Kernel::VERSION, '3.2.0') >= 0) {
            $loader->load(__DIR__.'/config_test_32.yml');
        }
    }
}