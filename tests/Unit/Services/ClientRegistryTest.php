<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\Services;

use Facile\MongoDbBundle\Event\ConnectionEvent;
use Facile\MongoDbBundle\Services\ClientRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Kernel;

class ClientRegistryTest extends TestCase
{
    use \Prophecy\PhpUnit\ProphecyTrait;

    public function test_client_connection_url_provided_manually()
    {
        $registry = new ClientRegistry($this->createEventDispatcherMock(), false, null);

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
        $registry = new ClientRegistry($this->createEventDispatcherMock(), false, null);

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
        $registry = new ClientRegistry($this->createEventDispatcherMock(), false, null);

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

    private function createEventDispatcherMock(): EventDispatcherInterface
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $event = Argument::that(function ($arg): bool {
            $this->assertInstanceOf(ConnectionEvent::class, $arg);
            $this->assertEquals('test_client.testdb', $arg->getClientName());

            return true;
        });

        if (Kernel::VERSION_ID >= 40300) {
            $eventDispatcher->dispatch($event, ConnectionEvent::CLIENT_CREATED)
                ->shouldBeCalledOnce()
                ->willReturnArgument(0);
        } else {
            $eventDispatcher->dispatch(ConnectionEvent::CLIENT_CREATED, $event)
                ->shouldBeCalledOnce()
                ->willReturnArgument(1);
        }

        return $eventDispatcher->reveal();
    }
}
