<?php declare(strict_types = 1);

namespace Facile\MongoDbBundle\Tests\Unit\Services;

use Facile\MongoDbBundle\Services\ClientRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientRegistryTest extends TestCase
{
    public function test_client_connection_url_generation_singlehost()
    {
        $ed = $this->prophesize(EventDispatcherInterface::class);


        $registry = new ClientRegistry($ed->reveal(), 'prod');

        $testConf = [
            'test_client' => [
                'hosts' => [
                    ['host' => 'host1', 'port' => 8080],
                ],
                'username' => 'foo',
                'password' => 'bar',
                'replicaSet' => 'testReplica',
                'ssl' => true,
                'connectTimeoutMS' => 3000,
            ],
        ];

        $registry->addClientsConfigurations($testConf);
        $client = $registry->getClient('test_client');

        $this->assertEquals('mongodb://host1:8080',$client->__debugInfo()['uri']);
    }
    public function test_client_connection_url_generation_multyhost()
    {
        $ed = $this->prophesize(EventDispatcherInterface::class);


        $registry = new ClientRegistry($ed->reveal(), 'prod');

        $testConf = [
            'test_client' => [
                'hosts' => [
                    ['host' => 'host1', 'port' => 8080],
                    ['host' => 'host2', 'port' => 8081],
                ],
                'username' => 'foo',
                'password' => 'bar',
                'replicaSet' => 'testReplica',
                'ssl' => true,
                'connectTimeoutMS' => 3000,
            ],
        ];

        $registry->addClientsConfigurations($testConf);
        $client = $registry->getClient('test_client');

        $this->assertEquals('mongodb://host1:8080,host2:8081',$client->__debugInfo()['uri']);
    }
}