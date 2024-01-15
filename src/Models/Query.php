<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Models;

/**
 * Class Query.
 *
 * @internal
 */
final class Query
{
    /** @var float */
    private $start;

    /** @var string */
    private $method;

    /** @var string */
    private $collection;

    /** @var array */
    private $filters;

    /** @var array|object */
    private $data;

    /** @var array */
    private $options;

    /** @var float */
    private $executionTime;

    /** @var string */
    private $readPreference;

    /** @var string */
    private $client;

    /** @var string */
    private $database;

    /**
     * Query constructor.
     */
    public function __construct()
    {
        $this->start = microtime(true);
        $this->client = 'undefined';
        $this->database = 'undefined';
        $this->collection = 'undefined';
        $this->method = 'undefined';
        $this->filters = [];
        $this->data = [];
        $this->options = [];
        $this->executionTime = 0;
        $this->readPreference = 'undefined';
    }

    public function getStart(): float
    {
        return $this->start;
    }

    public function getCollection(): string
    {
        return $this->collection;
    }

    public function setCollection(string $collection)
    {
        $this->collection = $collection;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array|object $filters
     */
    public function setFilters($filters)
    {
        $this->filters = (array) ($filters ?? []);
    }

    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|object $data
     */
    public function setData($data): void
    {
        $this->data = $data ?? [];
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    public function setExecutionTime(float $executionTime)
    {
        $this->executionTime = $executionTime;
    }

    public function getReadPreference(): string
    {
        return $this->readPreference;
    }

    public function setReadPreference(string $readPreference)
    {
        $this->readPreference = $readPreference;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function setClient(string $client)
    {
        $this->client = $client;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database)
    {
        $this->database = $database;
    }
}
