<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractContainerAwareFixture
{
    private ContainerInterface $container;

    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }
}
