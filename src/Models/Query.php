<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Models;

/**
 * Class Query.
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
    /** @var array|object */
    private $filters;
    /** @var array */
    private $data;
    /** @var array */
    private $options;
    /** @var int */
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

    /**
     * @return float
     */
    public function getStart(): float
    {
        return $this->start;
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @param string $collection
     */
    public function setCollection(string $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array|object $filters
     */
    public function setFilters($filters)
    {
        $this->filters = (array)$filters ?? [];
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
    public function setData($data)
    {
        $this->data = $data ?? [];
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return float
     */
    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    /**
     * @param float $executionTime
     */
    public function setExecutionTime(float $executionTime)
    {
        $this->executionTime = $executionTime;
    }

    /**
     * @return string
     */
    public function getReadPreference(): string
    {
        return $this->readPreference;
    }

    /**
     * @param string $readPreference
     */
    public function setReadPreference(string $readPreference)
    {
        $this->readPreference = $readPreference;
    }

    /**
     * @return string
     */
    public function getClient(): string
    {
        return $this->client;
    }

    /**
     * @param string $client
     */
    public function setClient(string $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @param string $database
     */
    public function setDatabase(string $database)
    {
        $this->database = $database;
    }
}
