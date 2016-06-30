<?php

declare(strict_types=1);

namespace MongoBundle\Tests\unit\Models;

use MongoBundle\Models\ConnectionConfiguration;

/**
 * Class ConnectionConfigurationTest.
 */
class ConnectionConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function test_construction()
    {
        $conf = new ConnectionConfiguration(
            'localhost',
            27017,
            'test',
            'admin',
            'admin_password'
        );

        $this->assertEquals('localhost', $conf->getHost());
        $this->assertEquals(27017, $conf->getPort());
        $this->assertEquals('test', $conf->getDatabase());
        $this->assertEquals('admin', $conf->getUsername());
        $this->assertEquals('admin_password', $conf->getPassword());
        $this->assertEquals('mongodb://admin:admin_password@localhost:27017/test', $conf->getConnectionUri());
    }

    public function test_construction_empty_credentials()
    {
        $conf = new ConnectionConfiguration(
            'localhost',
            27017,
            'test',
            '',
            ''
        );

        $this->assertEquals('localhost', $conf->getHost());
        $this->assertEquals(27017, $conf->getPort());
        $this->assertEquals('test', $conf->getDatabase());
        $this->assertEquals('', $conf->getUsername());
        $this->assertEquals('', $conf->getPassword());
        $this->assertEquals('mongodb://localhost:27017/test', $conf->getConnectionUri());
    }
}
