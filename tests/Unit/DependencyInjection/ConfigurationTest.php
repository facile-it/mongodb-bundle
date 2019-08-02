<?php

namespace Facile\MongoDbBundle\Tests\unit\DependencyInjection;

use Facile\MongoDbBundle\DependencyInjection\Configuration;
use Facile\MongoDbBundle\DependencyInjection\MongoDbBundleExtension;
use Matthias\SymfonyDependencyInjectionTest\Loader\ExtensionConfigurationBuilder;
use Matthias\SymfonyDependencyInjectionTest\Loader\LoaderFactory;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function test_empty_configuration_process()
    {
        $this->expectException(\Exception::class);
        $this->assertProcessedConfigurationEquals([
            'clients' => [],
            'connections' => [],
            'data_collection' => true,
        ], [
            __DIR__ . '/../../Fixtures/config/config_empty.yml',
        ]);
    }

    public function test_uri_configuration_process()
    {
        $expectedConfiguration = [
            'clients' => [
                'test_client' => [
                    'hosts' => [],
                    'uri' => 'mongodb://user:password@host1:27017',
                    'username' => '',
                    'password' => '',
                    'authSource' => null,
                    'replicaSet' => null,
                    'ssl' => false,
                    'connectTimeoutMS' => null,
                    'readPreference' => 'primaryPreferred',
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
            __DIR__ . '/../../Fixtures/config/config_uri.yml',
        ]);
    }

    public function test_full_configuration_process()
    {
        $expectedConfiguration = [
            'clients' => [
                'test_client' => [
                    'hosts' => [
                        ['host' => 'localhost', 'port' => 8080],
                    ],
                    'uri' => null,
                    'username' => 'foo',
                    'password' => 'bar',
                    'authSource' => null,
                    'replicaSet' => null,
                    'ssl' => false,
                    'connectTimeoutMS' => null,
                    'readPreference' => 'primaryPreferred',
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
            __DIR__ . '/../../Fixtures/config/config_full.yml',
        ]);
    }

    public function test_options_configuration_process()
    {
        $expectedConfiguration = [
            'clients' => [
                'test_client' => [
                    'hosts' => [
                        ['host' => 'localhost', 'port' => 8080],
                    ],
                    'uri' => null,
                    'username' => 'foo',
                    'password' => 'bar',
                    'authSource' => null,
                    'replicaSet' => 'testReplica',
                    'ssl' => true,
                    'connectTimeoutMS' => 3000,
                    'readPreference' => 'primaryPreferred',
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
            __DIR__ . '/../../Fixtures/config/config_options.yml',
        ]);
    }

    public function test_data_collection_disabled_configuration_process()
    {
        $expectedConfiguration = [
            'clients' => [
                'test_client' => [
                    'hosts' => [
                        ['host' => 'localhost', 'port' => 8080],
                    ],
                    'uri' => null,
                    'username' => 'foo',
                    'password' => 'bar',
                    'authSource' => null,
                    'replicaSet' => 'testReplica',
                    'ssl' => true,
                    'connectTimeoutMS' => 3000,
                    'readPreference' => 'primaryPreferred',
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
            __DIR__ . '/../../Fixtures/config/config_datacollection_disabled.yml',
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
                    'uri' => null,
                    'username' => 'foo',
                    'password' => 'bar',
                    'authSource' => null,
                    'replicaSet' => null,
                    'ssl' => false,
                    'connectTimeoutMS' => null,
                    'readPreference' => 'primaryPreferred',
                ],
                'other_client' => [
                    'hosts' => [
                        ['host' => 'localhost.dev', 'port' => 8081],
                        ['host' => 'localhost.dev2', 'port' => 27017],
                    ],
                    'uri' => null,
                    'username' => 'mee',
                    'password' => 'zod',
                    'authSource' => null,
                    'replicaSet' => null,
                    'ssl' => false,
                    'connectTimeoutMS' => null,
                    'readPreference' => 'primaryPreferred',
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
            __DIR__ . '/../../Fixtures/config/config_multiple.yml',
        ]);
    }

    public function test_configuration_blocks_invalid_read_preference_options()
    {
        $extensionConfigurationBuilder = new ExtensionConfigurationBuilder(new LoaderFactory());
        $extensionConfiguration = $extensionConfigurationBuilder
            ->setExtension($this->getContainerExtension())
            ->setSources([__DIR__ . '/../../Fixtures/config/config_wrong_readPreference.yml']);

        $processor = new Processor();
        $configuration = new Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration for path "mongo_db_bundle.clients.test_client.readPreference": Invalid readPreference option "fakeOption", must be one of [primary, primaryPreferred, secondary, secondaryPreferred, nearest]');
        $processor->processConfiguration(
            $configuration,
            $extensionConfiguration->getConfiguration()
        );
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
