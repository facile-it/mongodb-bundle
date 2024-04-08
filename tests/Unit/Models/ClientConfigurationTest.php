<?php

declare(strict_types=1);

namespace MongoBundle\Tests\unit\Models;

use Facile\MongoDbBundle\Models\ClientConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientConfigurationTest.
 */
class ClientConfigurationTest extends TestCase
{
    public function test_construction(): void
    {
        $conf = new ClientConfiguration(
            'localhost:27017',
            'admin',
            'admin_password'
        );

        self::assertEquals('localhost:27017', $conf->getUri());
        self::assertEquals('admin', $conf->getUsername());
        self::assertEquals('admin_password', $conf->getPassword());
        self::assertEquals(
            [
                'username' => 'admin',
                'password' => 'admin_password',
            ],
            $conf->getOptions()
        );
    }

    public function test_construction_empty_credentials(): void
    {
        $conf = new ClientConfiguration(
            'localhost:27017',
            '',
            ''
        );

        self::assertEquals('localhost:27017', $conf->getUri());
        self::assertEquals('', $conf->getUsername());
        self::assertEquals('', $conf->getPassword());
        self::assertEquals(
            [],
            $conf->getOptions()
        );
    }

    /**
     * @dataProvider optionsDataProvider
     */
    public function test_construction_with_options(array $options, array $expectedOptions): void
    {
        $conf = new ClientConfiguration(
            'localhost:27017',
            '',
            '',
            null,
            $options
        );

        self::assertEquals('localhost:27017', $conf->getUri());
        self::assertEquals('', $conf->getUsername());
        self::assertEquals('', $conf->getPassword());
        self::assertEquals(
            $expectedOptions,
            $conf->getOptions()
        );
    }

    public function optionsDataProvider(): array
    {
        return [
            [   // set 1
                [   // provided
                    'replicaSet' => '',
                    'ssl' => false,
                    'connectTimeoutMS' => '',
                ],
                [   // expected
                    'ssl' => false,
                ],
            ],
            [   // set 2
                [   // provided
                    'replicaSet' => 'testReplica',
                    'ssl' => true,
                    'connectTimeoutMS' => 100,
                ],
                [   // expected
                    'replicaSet' => 'testReplica',
                    'ssl' => true,
                    'connectTimeoutMS' => 100,
                ],
            ],
            [   // set 3
                [   // provided
                    'replicaSet' => null,
                    'ssl' => true,
                    'connectTimeoutMS' => null,
                    'readPreference' => 'primary',
                ],
                [   // expected
                    'ssl' => true,
                    'readPreference' => 'primary',
                ],
            ],
        ];
    }
}
