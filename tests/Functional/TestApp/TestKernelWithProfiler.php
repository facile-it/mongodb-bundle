<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\TestApp;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;

class TestKernelWithProfiler extends TestKernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [...parent::registerBundles(), new TwigBundle(), new WebProfilerBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/services.yaml');
        $loader->load(__DIR__ . '/config_profiler.yml');
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->setParameter('routing_config_dir', __DIR__);

        parent::build($container);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        // noop - 4.4 backward compat
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        // noop - 4.4 backward compat
    }
}
