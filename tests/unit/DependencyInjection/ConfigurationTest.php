<?php

namespace Facile\MongoDbBundle\Tests\unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Facile\MongoDbBundle\DependencyInjection\Configuration;
use Facile\MongoDbBundle\DependencyInjection\MongoBundleExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function test_empty_configuration_process()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child node "host" at path "mongo_bundle.connections.default" must be configured.');
        $this->assertProcessedConfigurationEquals([], [
            __DIR__.'/../../fixtures/config/config_empty.yml',
        ]);
    }

    public function test_minimal_configuration_process()
    {
        $expectedConfiguration = [
            'connections' => [
                'default' => [
                    'host' => 'localhost',
                    'database' => 'telegraf',
                    'port' => 27017,
                    'username' => '',
                    'password' => '',
                ],
            ],
        ];
        $this->assertProcessedConfigurationEquals($expectedConfiguration, [
            __DIR__.'/../../fixtures/config/config_minimal.yml',
        ]);
    }

    public function test_full_configuration_process()
    {
        $expectedConfiguration = [
            'default_connection' => 'default',
            'connections' => [
                'default' => [
                    'host' => 'localhost',
                    'database' => 'testdb',
                    'port' => 27017,
                    'username' => 'foo',
                    'password' => 'bar',
                ],
            ],
        ];
        $this->assertProcessedConfigurationEquals($expectedConfiguration, [
            __DIR__.'/../../fixtures/config/config_full.yml',
        ]);
    }

    public function test_multiple_connections_configuration_process()
    {
        $expectedConfiguration = [
            'default_connection' => 'test',
            'connections' => [
                'default' => [
                    'database' => 'telegraf',
                    'host' => 'localhost',
                    'port' => 27017,
                    'username' => 'foo',
                    'password' => 'bar',
                ],
                'test' => [
                    'database' => 'test',
                    'host' => 'localhost',
                    'port' => 27018,
                    'username' => 'foo',
                    'password' => 'bar',
                ],
                'test_again' => [
                    'database' => 'test_again',
                    'host' => 'localhost',
                    'port' => 27017,
                    'username' => 'foo',
                    'password' => 'bar',
                ],
            ],
        ];
        $this->assertProcessedConfigurationEquals($expectedConfiguration, [
            __DIR__.'/../../fixtures/config/config_multiple.yml',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtension()
    {
        return new MongoBundleExtension();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
