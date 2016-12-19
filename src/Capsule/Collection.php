<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Models\QueryLog;
use MongoDB\Collection as MongoCollection;
use MongoDB\Driver\Manager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Collection.
 * @internal
 */
final class Collection extends MongoCollection
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * Collection constructor.
     *
     * @param Manager                  $manager
     * @param string                   $databaseName
     * @param string                   $collectionName
     * @param array                    $options
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @internal param DataCollectorLoggerInterface $logger
     */
    public function __construct(Manager $manager, $databaseName, $collectionName, array $options = [], EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($manager, $databaseName, $collectionName, $options);
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function aggregate(array $pipeline, array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, null, $pipeline, $options);
        $result = parent::aggregate($pipeline, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function count($filter = [], array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, null, $options);
        $result = parent::count($filter, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function find($filter = [], array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, null, $options);
        $result = parent::find($filter, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOne($filter = [], array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, null, $options);
        $result = parent::findOne($filter, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneAndUpdate($filter, $update, array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, $update, $options);
        $result = parent::findOneAndUpdate($filter, $update, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneAndDelete($filter, array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, null, $options);
        $result = parent::findOneAndDelete($filter, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMany($filter, array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, null, $options);
        $result = parent::deleteMany($filter, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne($filter, array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, null, $options);
        $result = parent::deleteOne($filter, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOne($filter, $replacement, array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, $replacement, $options);
        $result = parent::replaceOne($filter, $replacement, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function insertOne($document, array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, [], $document, $options);
        $result = parent::insertOne($document, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function updateOne($filter, $update, array $options = [])
    {
        $event = $this->prepareEvent(__FUNCTION__, $filter, $update, $options);
        $result = parent::updateOne($filter, $update, $options);
        $this->notifyQueryExecution($event);

        return $result;
    }

    /**
     * @param string $method
     * @param array|object  $filters
     * @param array|object  $data
     * @param array  $options
     *
     * @return QueryLog
     */
    private function prepareEvent(string $method, $filters = null, $data = null, array $options): QueryLog
    {
        $debugInfo = $this->__debugInfo();

        $event = new QueryLog();
        $event->setFilters($filters);
        $event->setData($data);
        $event->setOptions($options);
        $event->setMethod($method);
        $event->setCollection($debugInfo['collectionName']);

        $this->eventDispatcher->dispatch(QueryEvent::QUERY_PREPARED, new QueryEvent($event));

        return $event;
    }

    /**
     * @param QueryLog $queryLog
     *
     * @return QueryLog
     */
    private function notifyQueryExecution(QueryLog $queryLog)
    {
        $queryLog->setExecutionTime(microtime(true) - $queryLog->getStart());

        $this->eventDispatcher->dispatch(QueryEvent::QUERY_EXECUTED, new QueryEvent($queryLog));
    }
}

