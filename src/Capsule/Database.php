<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use MongoDB\Database as MongoDatabase;
use MongoDB\Driver\Manager;

/**
 * Class Database.
 */
final class Database extends MongoDatabase
{
    /**
     * @var DataCollectorLoggerInterface
     */
    private $logger;

    /**
     * Database constructor.
     *
     * @param Manager                      $manager
     * @param string                       $databaseName
     * @param array                        $options
     * @param DataCollectorLoggerInterface $logger
     */
    public function __construct(Manager $manager, $databaseName, array $options = [], DataCollectorLoggerInterface $logger)
    {
        parent::__construct($manager, $databaseName, $options);
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function selectCollection($collectionName, array $options = [])
    {
        $debug = $this->__debugInfo();
        $options += [
            'readConcern' => $debug['readConcern'],
            'readPreference' => $debug['readPreference'],
            'typeMap' => $debug['typeMap'],
            'writeConcern' => $debug['writeConcern'],
        ];

        return new Collection($debug['manager'], $debug['databaseName'], $collectionName, $options, $this->logger);
    }
}
