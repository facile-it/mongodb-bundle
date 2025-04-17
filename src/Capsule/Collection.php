<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Capsule;

use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Models\Query;
use MongoDB\Collection as MongoCollection;
use MongoDB\DeleteResult;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class Collection extends MongoCollection
{
    private EventDispatcherInterface $eventDispatcher;

    private string $clientName;

    private string $databaseName;

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

    public function aggregate(array|\MongoDB\Builder\Pipeline $pipeline, array $options = []): CursorInterface
    {
        $query = $this->prepareQuery(__FUNCTION__, [], $pipeline, $options);
        $result = parent::aggregate($query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function count($filter = [], array $options = []): int
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::count($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function countDocuments($filter = [], array $options = []): int
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::countDocuments($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function find($filter = [], array $options = []): CursorInterface
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::find($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function findOne($filter = [], array $options = []): array|object|null
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::findOne($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function findOneAndUpdate($filter, $update, array $options = []): array|object|null
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, $update, $options);
        $result = parent::findOneAndUpdate($query->getFilters(), $query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function findOneAndDelete($filter, array $options = []): array|object|null
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::findOneAndDelete($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function deleteMany($filter, array $options = []): DeleteResult
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::deleteMany($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function deleteOne($filter, array $options = []): DeleteResult
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, [], $options);
        $result = parent::deleteOne($query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function replaceOne($filter, $replacement, array $options = []): UpdateResult
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, $replacement, $options);
        $result = parent::replaceOne($query->getFilters(), $query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function insertOne($document, array $options = []): InsertOneResult
    {
        $query = $this->prepareQuery(__FUNCTION__, [], $document, $options);
        $result = parent::insertOne($query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function updateOne($filter, $update, array $options = []): UpdateResult
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, $update, $options);
        $result = parent::updateOne($query->getFilters(), $query->getData(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function distinct($fieldName, $filter = [], array $options = []): array
    {
        $query = $this->prepareQuery(__FUNCTION__, $filter, ['fieldName' => $fieldName], $options);
        $result = parent::distinct($fieldName, $query->getFilters(), $query->getOptions());
        $this->notifyQueryExecution($query);

        return $result;
    }

    public function estimatedDocumentCount(array $options = []): int
    {
        $query = $this->prepareQuery(__FUNCTION__, [], [], $options);
        $result = parent::estimatedDocumentCount($options);
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
        if (method_exists(ReadPreference::class, 'getModeString')) {
            return $readPreference->getModeString();
        }

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

    private function notifyQueryExecution(Query $queryLog): void
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
