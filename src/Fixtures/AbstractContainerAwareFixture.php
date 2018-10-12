<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractContainerAwareFixture.
 */
abstract class AbstractContainerAwareFixture
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
