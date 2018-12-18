<?php

declare(strict_types=1);

namespace MongoBundle\Tests\unit\Models;

use Facile\MongoDbBundle\Models\ClientConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientConfigurationTest.
 */
class ClientConfigurationTest extends TestCase
{
    public function test_construction()
    {
        $conf = new ClientConfiguration(
            'mongodb://localhost:27017',
            [
                'username' => 'admin',
                'password' => 'admin_password',
            ]
        );

        self::assertEquals('mongodb://localhost:27017', $conf->getUri());
        self::assertEquals(
            [
                'username' => 'admin',
                'password' => 'admin_password',
            ],
            $conf->getOptions()
        );
    }
}
