<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use Facile\MongoDbBundle\Services\Loggers\Model\LogEvent;
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
    public function find($filter = [], array $options = [])
    {
        $event = $this->startQueryLogging($filter, __FUNCTION__);
        $result = parent::find($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOne($filter = [], array $options = [])
    {
        $event = $this->startQueryLogging($filter, __FUNCTION__);
        $result = parent::findOne($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneAndUpdate($filter, $update, array $options = [])
    {
        $event = $this->startQueryLogging($filter, __FUNCTION__);
        $result = parent::findOneAndUpdate($filter, $update, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneAndDelete($filter, array $options = [])
    {
        $event = $this->startQueryLogging($filter, __FUNCTION__);
        $result = parent::findOneAndDelete($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMany($filter, array $options = [])
    {
        $event = $this->startQueryLogging($filter, __FUNCTION__);
        $result = parent::deleteMany($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne($filter, array $options = [])
    {
        $event = $this->startQueryLogging($filter, __FUNCTION__);
        $result = parent::deleteOne($filter, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOne($filter, $replacement, array $options = [])
    {
        $data = [
            "filter" => $filter,
            "replacement" => is_array($replacement) ? $replacement : $replacement->toArray()
        ];
        $event = $this->startQueryLogging($data, __FUNCTION__);
        $result = parent::replaceOne($filter, $replacement, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function insertOne($document, array $options = [])
    {
        $event = $this->startQueryLogging($document, __FUNCTION__);
        $result = parent::insertOne($document, $options);
        $this->logger->logQuery($event);

        return $result;
    }

    /**
     * @param        $filter
     * @param string $method
     *
     * @return LogEvent
     */
    private function startQueryLogging($filter, string $method): LogEvent
    {
        $debugInfo = $this->__debugInfo();

        $event = new LogEvent();
        $event->setData($filter);
        $event->setMethod($method);
        $event->setCollection($debugInfo['collectionName']);

        $this->logger->startLogging($event);

        return $event;
    }
}

