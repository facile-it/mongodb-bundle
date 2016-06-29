<?php

declare(strict_types=1);

namespace MongoBundle\Tests\unit;

use MongoBundle\DependencyInjection\MongoBundleExtension;
use MongoBundle\MongoBundle;

class MongoBundleTest extends \PHPUnit_Framework_TestCase
{
    public function test_bundle()
    {
        $bundle = new MongoBundle();
        $this->assertInstanceOf(MongoBundleExtension::class, $bundle->getContainerExtension());
    }
}
