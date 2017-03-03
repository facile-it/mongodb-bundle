<?php

namespace Facile\MongoDbBundle\Tests\unit\DependencyInjection;

use Facile\MongoDbBundle\DependencyInjection\Configuration;
use Facile\MongoDbBundle\DependencyInjection\MongoDbBundleExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function test_empty_configuration_process()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child node "clients" at path "mongo_db_bundle" must be configured.');
        $this->assertProcessedConfigurationEquals([
            'clients' => [],
            'connections' => [],
            'data_collection' => true,
        ], [
            __DIR__.'/../../fixtures/config/config_empty.yml',
        ]);
    }

    public function test_full_configuration_process()
    {
        $expectedConfiguration = [
            'clients' => [
                'test_client' => [
                    'hosts' => [
                        ['host' => 'localhost', 'port' => 8080]
                    ],
                    'username' => 'foo',
                    'password' => 'bar',
                    'replicaSet' => null,
                    'ssl' => false,
                    'connectTimeoutMS' => null,
                ],
            ],
            'connections' => [
                'test_db' => [
                    'client_name' => 'test_client',
                    'database_name' => 'testdb',
                ],
            ],
            'data_collection' => true,
        ];
        $this->assertProcessedConfigurationEquals($expectedConfiguration, [
            __DIR__.'/../../fixtures/config/config_full.yml',
        ]);
    }

    public function test_options_configuration_process()
    {
        $expectedConfiguration = [
            'clients' => [
                'test_client' => [
                    'hosts' => [
                        ['host' => 'localhost', 'port' => 8080]
                    ],
                    'username' => 'foo',
                    'password' => 'bar',
                    'replicaSet' => 'testReplica',
                    'ssl' => true,
                    'connectTimeoutMS' => 3000,
                ],
            ],
            'connections' => [
                'test_db' => [
                    'client_name' => 'test_client',
                    'database_name' => 'testdb',
                ],
            ],
            'data_collection' => true,
        ];
        $this->assertProcessedConfigurationEquals($expectedConfiguration, [
            __DIR__.'/../../fixtures/config/config_options.yml',
        ]);
    }

    public function test_data_collection_disabled_configuration_process()
    {
        $expectedConfiguration = [
            'clients' => [
                'test_client' => [
                    'hosts' => [
                        ['host' => 'localhost', 'port' => 8080]
                    ],
                    'username' => 'foo',
                    'password' => 'bar',
                    'replicaSet' => 'testReplica',
                    'ssl' => true,
                    'connectTimeoutMS' => 3000,
                ],
            ],
            'connections' => [
                'test_db' => [
                    'client_name' => 'test_client',
                    'database_name' => 'testdb',
                ],
            ],
            'data_collection' => false,
        ];
        $this->assertProcessedConfigurationEquals($expectedConfiguration, [
            __DIR__.'/../../fixtures/config/config_datacollection_disabled.yml',
        ]);
    }

    public function test_multiple_connections_configuration_process()
    {
        $expectedConfiguration = [
            'clients' => [
                'test_client' => [
                    'hosts' => [
                        ['host' => 'localhost', 'port' => 8080],
                    ],
                    'username' => 'foo',
                    'password' => 'bar',
                    'replicaSet' => null,
                    'ssl' => false,
                    'connectTimeoutMS' => null,
                ],
                'other_client' => [
                    'hosts' => [
                        ['host' => 'localhost.dev', 'port' => 8081],
                        ['host' => 'localhost.dev2', 'port' => 27017]
                    ],
                    'username' => 'mee',
                    'password' => 'zod',
                    'replicaSet' => null,
                    'ssl' => false,
                    'connectTimeoutMS' => null,
                ],
            ],
            'connections' => [
                'test_db' => [
                    'client_name' => 'test_client',
                    'database_name' => 'testdb',
                ],
                'other_db' => [
                    'client_name' => 'other_client',
                    'database_name' => 'otherdb',
                ],
                'test_db_2' => [
                    'client_name' => 'test_client',
                    'database_name' => 'testdb_2',
                ],
            ],
            'data_collection' => true,
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
        return new MongoDbBundleExtension();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
