<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Capsule;

use MongoDB\Database as MongoDatabase;

/**
 * Class Database.
 */
class Database extends MongoDatabase
{
    /**
     * @inheritdoc
     */
    public function selectCollection(string $collectionName, array $options = [])
    {
        $debug = $this->__debugInfo();
        $options += [
            'readConcern' => $debug['readConcern'],
            'readPreference' => $debug['readPreference'],
            'typeMap' => $debug['typeMap'],
            'writeConcern' => $debug['writeConcern'],
        ];

        return new Collection($debug['manager'], $debug['databaseName'], $collectionName, $options);
    }
}
