<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle;

use Facile\MongoDbBundle\DependencyInjection\MongoDbBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class FacileMongoDbBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new MongoDbBundleExtension();
    }
}
