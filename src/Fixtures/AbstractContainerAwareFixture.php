<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractContainerAwareFixture
{
    /** @var ContainerInterface */
    private $container;

    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
