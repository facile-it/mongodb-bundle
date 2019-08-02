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
        $suffix = '';
        $version = '';

        if ('docker' === getenv('TEST_ENV')) {
            $suffix = '_docker';
        }

        if (version_compare(Kernel::VERSION, '3.2.0') >= 0) {
            $version = '_32';
        }

        $configFile = sprintf('/config_test%s%s.yml', $version, $suffix);
        $loader->load(__DIR__ . $configFile);
    }
}
