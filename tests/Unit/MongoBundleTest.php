<?php

declare(strict_types=1);

namespace MongoBundle\Tests\unit;

use Facile\MongoDbBundle\DependencyInjection\MongoDbBundleExtension;
use Facile\MongoDbBundle\FacileMongoDbBundle;
use PHPUnit\Framework\TestCase;

class MongoBundleTest extends TestCase
{
    public function test_bundle(): void
    {
        $bundle = new FacileMongoDbBundle();
        $this->assertInstanceOf(MongoDbBundleExtension::class, $bundle->getContainerExtension());
    }
}
