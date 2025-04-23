<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use MongoDB\Database as MongoDatabase;
use MongoDB\Driver\Manager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Database.
 *
 * @internal
 */
final class Database extends MongoDatabase
{
    /**
     * Database constructor.
     *
     * @internal param DataCollectorLoggerInterface $logger
     */
    public function __construct(
        Manager $manager,
        private readonly string $clientName,
        private readonly string $databaseName,
        array $options,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($manager, $this->databaseName, $options);
    }

    public function selectCollection($collectionName, array $options = []): Collection
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
            $this->clientName,
            $this->databaseName,
            $collectionName,
            $options,
            $this->eventDispatcher
        );
    }

    public function withOptions(array $options = []): self
    {
        $debug = $this->__debugInfo();
        $options += [
            'readConcern' => $debug['readConcern'],
            'readPreference' => $debug['readPreference'],
            'typeMap' => $debug['typeMap'],
            'writeConcern' => $debug['writeConcern'],
        ];

        return new self($debug['manager'], $this->clientName, $debug['databaseName'], $options, $this->eventDispatcher);
    }
}
