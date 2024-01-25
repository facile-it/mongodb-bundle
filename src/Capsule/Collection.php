<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Models\Query;
use MongoDB\Collection as MongoCollection;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class Collection extends MongoCollection
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string */
    private $clientName;

    /** @var string */
    private $databaseName;

    public function __construct(
        Manager $manager,
        string $clientName,
        string $databaseName,
        string $collectionName,
        array $options,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($manager, $databaseName, $collectionName, $options);
        $this->eventDispatcher = $eventDispatcher;
        $this->clientName = $clientName;
        $this->databaseName = $databaseName;
    }

    /**
     * @inheritDoc
     */
    public function aggregate(array $pipeline, array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, [], $pipeline, $options);
        $result = parent::aggregate($query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function count($filter = [], array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::count($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function find($filter = [], array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::find($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function findOne($filter = [], array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::findOne($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function findOneAndUpdate($filter, $update, array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, $update, $options);
        $result = parent::findOneAndUpdate($query->getFilters(), $query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function findOneAndDelete($filter, array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::findOneAndDelete($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function deleteMany($filter, array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::deleteMany($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function deleteOne($filter, array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::deleteOne($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function replaceOne($filter, $replacement, array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, $replacement, $options);
        $result = parent::replaceOne($query->getFilters(), $query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function insertOne($document, array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, [], $document, $options);
        $result = parent::insertOne($query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function updateOne($filter, $update, array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, $update, $options);
        $result = parent::updateOne($query->getFilters(), $query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function distinct($fieldName, $filter = [], array $options = [])
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, ['fieldName' => $fieldName], $options);
        $result = parent::distinct($fieldName, $query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    /**
     * @param array|object $filters
     * @param array|object $data
     */
    private function prepareQuery(string $method, $filters, $data, array $options): Query
    {
        $query = new Query();
        $query->setFilters($filters ?? []);
        $query->setData($data ?? []);
        $query->setOptions($options);
        $query->setMethod($method);
        $query->setClient($this->getClientName());
        $query->setDatabase($this->getDatabaseName());
        $query->setCollection($this->getCollectionName());
        $query->setReadPreference(
            $this->translateReadPreference($options['readPreference'] ?? $this->__debugInfo()['readPreference'])
        );

        $event = new QueryEvent($query);
        $this->eventDispatcher->dispatch($event, QueryEvent::QUERY_PREPARED);

        return $query;
    }

    private function translateReadPreference(ReadPreference $readPreference): string
    {
        switch ($readPreference->getMode()) {
            case ReadPreference::RP_PRIMARY:
                return 'primary';
            case ReadPreference::RP_PRIMARY_PREFERRED:
                return 'primaryPreferred';
            case ReadPreference::RP_SECONDARY:
                return 'secondary';
            case ReadPreference::RP_SECONDARY_PREFERRED:
                return 'secondaryPreferred';
            case ReadPreference::RP_NEAREST:
                return 'nearest';
            default:
                return 'undefined';
        }
    }

    private function notifyQueryExecution(Query $queryLog)
    {
        $queryLog->setExecutionTime(microtime(true) - $queryLog->getStart());

        $this->eventDispatcher->dispatch(new QueryEvent($queryLog), QueryEvent::QUERY_EXECUTED);
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }
}
