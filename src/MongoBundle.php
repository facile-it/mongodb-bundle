<?php

declare(strict_types = 1);

namespace MongoBundle;

use MongoBundle\DependencyInjection\MongoBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MongoBundle
 */
final class MongoBundle extends Bundle
{
    /**
     * @return MongoBundleExtension
     */
    public function getContainerExtension()
    {
        return new MongoBundleExtension();
    }
}
