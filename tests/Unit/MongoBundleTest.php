<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit;

use Facile\MongoDbBundle\DependencyInjection\MongoDbBundleExtension;
use Facile\MongoDbBundle\FacileMongoDbBundle;
use PHPUnit\Framework\TestCase;

class MongoBundleTest extends TestCase
{
    public function test_bundle()
    {
        $bundle = new FacileMongoDbBundle();
        $this->assertInstanceOf(MongoDbBundleExtension::class, $bundle->getContainerExtension());
    }
}
