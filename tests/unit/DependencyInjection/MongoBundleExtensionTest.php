<?php

namespace MongoBundle\Tests\unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use MongoBundle\DependencyInjection\MongoBundleExtension;
use MongoDB\Client;
use MongoDB\Database;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class MongoBundleExtensionTest extends AbstractExtensionTestCase
{
    public function test_load()
    {
        $this->load(
            [
                'host' => 'localhost',
                'database' => 'telegraf',
            ]
        );
        $this->compile();
        // Alias connections
        $this->assertContainerBuilderHasService('mongo.connection', Client::class);
        $defaultConnection = $this->container->get('mongo.connection');
        $this->assertInstanceOf(Database::class, $defaultConnection);
        $this->assertSame('telegraf', $defaultConnection->getDatabaseName());
        // 'default' connections
        $this->assertContainerBuilderHasService('mongo.connection.default', Client::class);
        $defaultConnection = $this->container->get('mongo.connection.default');

        $this->assertInstanceOf(Database::class, $defaultConnection);
        $this->assertSame('telegraf', $defaultConnection->getDatabaseName());
    }

    public function test_load_multiple_connections()
    {
        $this->markTestIncomplete();
    }

    /**
     * Return an array of container extensions you need to be registered for each test (usually just the container
     * extension you are testing.
     *
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [
            new MongoBundleExtension(),
        ];
    }
}