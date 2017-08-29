<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

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
    /** @var string */
    private $clientName;
    /** @var string */
    private $databaseName;

    /**
     * Database constructor.
     *
     * @param Manager                  $manager
     * @param string                   $clientName
     * @param string                   $databaseName
     * @param array                    $options
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @internal param DataCollectorLoggerInterface $logger
     */
    public function __construct(
        Manager $manager,
        string $clientName,
        string $databaseName,
        array $options = [],
        EventDispatcherInterface $eventDispatcher
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
            $this->clientName,
            $this->databaseName,
            $collectionName,
            $options,
            $this->eventDispatcher
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

        return new self($debug['manager'], $this->clientName, $debug['databaseName'], $options, $this->eventDispatcher);
    }
}
