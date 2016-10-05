<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Models;

/**
 * Class LogEvent.
 * @internal
 */
class LogEvent
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

    /**
     * LogEvent constructor.
     */
    public function __construct()
    {
        $this->start = 0.0;
        $this->collection = 'undefined';
        $this->method = 'undefined';
        $this->filters = [];
        $this->data = [];
        $this->options = [];
        $this->executionTime = 0;
    }

    /**
     * @return float
     */
    public function getStart(): float
    {
        return $this->start;
    }

    /**
     * @param float $start
     */
    public function setStart(float $start)
    {
        $this->start = $start;
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
        $this->filters = (array) $filters ?? [];
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
}
