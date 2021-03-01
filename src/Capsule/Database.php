<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use MongoDB\Database as MongoDatabase;
use MongoDB\Driver\Manager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class Database extends MongoDatabase
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string */
    private $clientName;

    /** @var string */
    private $databaseName;

    /**
     * @param Manager $manager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $clientName
     * @param string $databaseName
     * @param array $options
     */
    public function __construct(
        Manager $manager,
        EventDispatcherInterface $eventDispatcher,
        string $clientName,
        string $databaseName,
        array $options = []
    ) {
        parent::__construct($manager, $databaseName, $options);
        $this->eventDispatcher = $eventDispatcher;
        $this->clientName = $clientName;
        $this->databaseName = $databaseName;
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

        return new Collection(
            $debug['manager'],
            $this->eventDispatcher,
            $this->clientName,
            $this->databaseName,
            $collectionName,
            $options
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withOptions(array $options = [])
    {
        $debug = $this->__debugInfo();
        $options += [
            'readConcern' => $debug['readConcern'],
            'readPreference' => $debug['readPreference'],
            'typeMap' => $debug['typeMap'],
            'writeConcern' => $debug['writeConcern'],
        ];

        return new self($debug['manager'], $this->eventDispatcher, $this->clientName, $debug['databaseName'], $options);
    }
}
