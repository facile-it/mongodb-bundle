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
    private readonly float $start;

    private string $method = 'undefined';

    private string $collection = 'undefined';

    private array $filters = [];

    /** @var array|object */
    private $data = [];

    private array $options = [];

    /** @var float */
    private $executionTime = 0;

    private string $readPreference = 'undefined';

    private string $client = 'undefined';

    private string $database = 'undefined';

    public function __construct()
    {
        $this->start = microtime(true);
    }

    public function getStart(): float
    {
        return $this->start;
    }

    public function getCollection(): string
    {
        return $this->collection;
    }

    public function setCollection(string $collection): void
    {
        $this->collection = $collection;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
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
    public function setFilters($filters): void
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

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    public function setExecutionTime(float $executionTime): void
    {
        $this->executionTime = $executionTime;
    }

    public function getReadPreference(): string
    {
        return $this->readPreference;
    }

    public function setReadPreference(string $readPreference): void
    {
        $this->readPreference = $readPreference;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function setClient(string $client): void
    {
        $this->client = $client;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database): void
    {
        $this->database = $database;
    }
}
