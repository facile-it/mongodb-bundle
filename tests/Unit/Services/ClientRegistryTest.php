<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\Services;

use Facile\MongoDbBundle\Services\ClientRegistry;
use Facile\MongoDbBundle\Services\DriverOptionsFactory;
use Facile\MongoDbBundle\Services\DriverOptionsInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientRegistryTest extends TestCase
{
    public function test_client_connection_url_provided_manually()
    {
        $ed = $this->prophesize(EventDispatcherInterface::class);
        $di = new DriverOptionsFactory();

        $registry = new ClientRegistry($ed->reveal(), $di, false);

        $testConf = [
            'test_client' => [
                'hosts' => [],
                'uri' => 'mongodb://user:password@host1:27017',
                'username' => '',
                'password' => '',
                'authSource' => null,
                'replicaSet' => 'testReplica',
                'ssl' => true,
                'connectTimeoutMS' => 3000,
                'readPreference' => 'primary',
            ],
        ];

        $registry->addClientsConfigurations($testConf);
        $client = $registry->getClient('test_client', 'testdb');

        $this->assertEquals('mongodb://user:password@host1:27017', $client->__debugInfo()['uri']);

        $this->assertEquals(['test_client.testdb'], $registry->getClientNames());
    }

    public function test_client_connection_url_generation_singlehost()
    {
        $ed = $this->prophesize(EventDispatcherInterface::class);
        $di = new DriverOptionsFactory();

        $registry = new ClientRegistry($ed->reveal(), $di, false);

        $testConf = [
            'test_client' => [
                'hosts' => [
                    ['host' => 'host1', 'port' => 8080],
                ],
                'uri' => null,
                'username' => 'foo',
                'password' => 'bar',
                'authSource' => null,
                'replicaSet' => 'testReplica',
                'ssl' => true,
                'connectTimeoutMS' => 3000,
                'readPreference' => 'primary',
            ],
        ];

        $registry->addClientsConfigurations($testConf);
        $client = $registry->getClient('test_client', 'testdb');

        $this->assertEquals('mongodb://host1:8080', $client->__debugInfo()['uri']);

        $this->assertEquals(['test_client.testdb'], $registry->getClientNames());
    }

    public function test_client_connection_url_generation_multihost()
    {
        $ed = $this->prophesize(EventDispatcherInterface::class);
        $di = new DriverOptionsFactory();
        
        $registry = new ClientRegistry($ed->reveal(), $di, false);

        $testConf = [
            'test_client' => [
                'hosts' => [
                    ['host' => 'host1', 'port' => 8080],
                    ['host' => 'host2', 'port' => 8081],
                ],
                'uri' => null,
                'username' => 'foo',
                'password' => 'bar',
                'authSource' => null,
                'replicaSet' => 'testReplica',
                'ssl' => true,
                'connectTimeoutMS' => 3000,
                'readPreference' => 'primary',
            ],
        ];

        $registry->addClientsConfigurations($testConf);
        $client = $registry->getClient('test_client', 'testdb');

        $this->assertEquals('mongodb://host1:8080,host2:8081', $client->__debugInfo()['uri']);
    }
}
