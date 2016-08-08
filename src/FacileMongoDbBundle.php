<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle;

use Facile\MongoDbBundle\DependencyInjection\MongoBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class FacileMongoDbBundle.
 */
final class FacileMongoDbBundle extends Bundle
{
    /**
     * @return MongoBundleExtension
     */
    public function getContainerExtension()
    {
        return new MongoBundleExtension();
    }
}
