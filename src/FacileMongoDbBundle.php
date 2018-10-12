<?php declare(strict_types=1);

namespace Facile\MongoDbBundle;

use Facile\MongoDbBundle\DependencyInjection\MongoDbBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class FacileMongoDbBundle.
 */
final class FacileMongoDbBundle extends Bundle
{
    /**
     * @return MongoDbBundleExtension
     */
    public function getContainerExtension()
    {
        return new MongoDbBundleExtension();
    }
}
