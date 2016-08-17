<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use MongoDB\Client as MongoClient;

/**
 * Class Client.
 */
final class Client extends MongoClient
{
    /** @var DataCollectorLoggerInterface */
    private $logger;

    /**
     * Client constructor.
     *
     * @param string                       $uri
     * @param array                        $uriOptions
     * @param array                        $driverOptions
     * @param DataCollectorLoggerInterface $logger
     */
    public function __construct($uri = 'mongodb://localhost:27017', array $uriOptions = [], array $driverOptions = [], DataCollectorLoggerInterface $logger)
    {
        parent::__construct($uri, $uriOptions, $driverOptions);
        $this->logger = $logger;
    }
    /**
     * {@inheritdoc}
     */
    public function selectDatabase($databaseName, array $options = [])
    {
        $debug = $this->__debugInfo();
        $options += [
            'typeMap' => $debug['typeMap'],
        ];

        return new Database($debug['manager'], $databaseName, $options, $this->logger);
    }

    /**
     * {@inheritdoc}
     */
    public function selectCollection($databaseName, $collectionName, array $options = [])
    {
        $debug = $this->__debugInfo();
        $options += [
            'typeMap' => $debug['typeMap'],
        ];

        return new Collection($debug['manager'], $databaseName, $collectionName, $options, $this->logger);
    }
}
