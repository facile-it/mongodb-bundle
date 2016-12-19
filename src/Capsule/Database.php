<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use MongoDB\Database as MongoDatabase;
use MongoDB\Driver\Manager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Database.
 * @internal
 */
final class Database extends MongoDatabase
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * Database constructor.
     *
     * @param Manager                  $manager
     * @param string                   $databaseName
     * @param array                    $options
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @internal param DataCollectorLoggerInterface $logger
     */
    public function __construct(Manager $manager, $databaseName, array $options = [], EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($manager, $databaseName, $options);
        $this->eventDispatcher = $eventDispatcher;
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

        return new Collection($debug['manager'], $debug['databaseName'], $collectionName, $options, $this->eventDispatcher);
    }
}
