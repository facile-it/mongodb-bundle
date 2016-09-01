<?php

declare(strict_types=1);

namespace MongoBundle\Tests\unit;

use Facile\MongoDbBundle\DependencyInjection\MongoDbBundleExtension;
use Facile\MongoDbBundle\FacileMongoDbBundle;

class MongoBundleTest extends \PHPUnit_Framework_TestCase
{
    public function test_bundle()
    {
        $bundle = new FacileMongoDbBundle();
        $this->assertInstanceOf(MongoDbBundleExtension::class, $bundle->getContainerExtension());
    }
}
