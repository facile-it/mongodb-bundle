<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Capsule;

use MongoDB\Client as MongoClient;

/**
 * Class Client.
 */
class Client extends MongoClient
{
    /**
     * @inheritdoc
     */
    public function selectDatabase($databaseName, array $options = [])
    {
        $debug = $this->__debugInfo();
        $options += [
            'typeMap' => $debug['typeMap'],
        ];

        return new Database($debug['manager'], $databaseName, $options);
    }

    /**
     * @inheritdoc
     */
    public function selectCollection($databaseName, $collectionName, array $options = [])
    {
        $debug = $this->__debugInfo();
        $options += [
            'typeMap' => $debug['typeMap'],
        ];

        return new Collection($debug['manager'], $databaseName, $collectionName, $options);
    }
}
