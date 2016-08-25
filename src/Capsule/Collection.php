<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use Facile\MongoDbBundle\Models\LogEvent;
use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use MongoDB\Collection as MongoCollection;
use MongoDB\Driver\Manager;

/**
 * Class Collection.
 */
final class Collection extends MongoCollection
{
    /**
     * @var DataCollectorLoggerInterface
     */
    private $logger;

    /**
     * Collection constructor.
     *
     * @param Manager                      $manager
     * @param string                       $databaseName
     * @param string                       $collectionName
     * @param array                        $options
     * @param DataCollectorLoggerInterface $logger
     */
    public function __construct(Manager $manager, $databaseName, $collectionName, array $options = [], DataCollectorLoggerInterface $logger)
    {
        parent::__construct($manager, $databaseName, $collectionName, $options);
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function count($filter = [], array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, $filter, null, $options);
        $result = parent::count($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function find($filter = [], array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, $filter, null, $options);
        $result = parent::find($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOne($filter = [], array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, $filter, null, $options);
        $result = parent::findOne($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneAndUpdate($filter, $update, array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, $filter, $update, $options);
        $result = parent::findOneAndUpdate($filter, $update, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneAndDelete($filter, array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, $filter, null, $options);
        $result = parent::findOneAndDelete($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMany($filter, array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, $filter, null, $options);
        $result = parent::deleteMany($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne($filter, array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, $filter, null, $options);
        $result = parent::deleteOne($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOne($filter, $replacement, array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, $filter, $replacement, $options);
        $result = parent::replaceOne($filter, $replacement, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function insertOne($document, array $options = [])
    {
        $event = $this->startQueryLogging(__FUNCTION__, [], $document, $options);
        $result = parent::insertOne($document, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * @param string $method
     * @param array  $filters
     * @param array  $data
     * @param array  $options
     *
     * @return LogEvent
     */
    private function startQueryLogging(string $method, array $filters, $data = null, array $options): LogEvent
    {
        $debugInfo = $this->__debugInfo();

        $event = new LogEvent();
        $event->setFilters($filters);
        $event->setData($data);
        $event->setOptions($options);
        $event->setMethod($method);
        $event->setCollection($debugInfo['collectionName']);

        $this->logger->startLogging($event);

        return $event;
    }
}

