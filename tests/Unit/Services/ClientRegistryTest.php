<?php declare(strict_types=1);

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
                'uri' => 'mongodb://foo:bar@host1:8080',
                'options' => [
                    'authSource' => null,
                    'replicaSet' => 'testReplica',
                    'ssl' => true,
                    'connectTimeoutMS' => 3000,
                    'readPreference' => 'primary',
                ],
            ],
        ];

        $registry->addClientsConfigurations($testConf);
        $client = $registry->getClient('test_client', 'testdb');

        $this->assertEquals('mongodb://foo:bar@host1:8080', $client->__debugInfo()['uri']);

        $this->assertEquals(['test_client.testdb'], $registry->getClientNames());
    }

    public function test_client_connection_url_generation_multyhost()
    {
        $ed = $this->prophesize(EventDispatcherInterface::class);

        $registry = new ClientRegistry($ed->reveal(), 'prod');

        $testConf = [
            'test_client' => [
                'uri' => 'mongodb://foo:bar@host1:8080,host2:8081',
                'options' => [
                    'authSource' => null,
                    'replicaSet' => 'testReplica',
                    'ssl' => true,
                    'connectTimeoutMS' => 3000,
                    'readPreference' => 'primary',
                ],
            ],
        ];

        $registry->addClientsConfigurations($testConf);
        $client = $registry->getClient('test_client', 'testdb');

        $this->assertEquals('mongodb://foo:bar@host1:8080,host2:8081', $client->__debugInfo()['uri']);
    }
}
