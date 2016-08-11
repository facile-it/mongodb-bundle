<?php

declare(strict_types=1);

namespace MongoBundle\Tests\unit\Models;

use Facile\MongoDbBundle\Models\ClientConfiguration;

/**
 * Class ClientConfigurationTest.
 */
class ClientConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function test_construction()
    {
        $conf = new ClientConfiguration(
            'localhost',
            27017,
            'admin',
            'admin_password'
        );

        $this->assertEquals('localhost', $conf->getHost());
        $this->assertEquals(27017, $conf->getPort());
        $this->assertEquals('admin', $conf->getUsername());
        $this->assertEquals('admin_password', $conf->getPassword());
    }

    public function test_construction_empty_credentials()
    {
        $conf = new ClientConfiguration(
            'localhost',
            27017,
            '',
            ''
        );

        $this->assertEquals('localhost', $conf->getHost());
        $this->assertEquals(27017, $conf->getPort());
        $this->assertEquals('', $conf->getUsername());
        $this->assertEquals('', $conf->getPassword());
    }
}
