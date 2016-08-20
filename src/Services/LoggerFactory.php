<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Services\Loggers\DataCollectorLoggerInterface;
use Facile\MongoDbBundle\Services\Loggers\MongoLogger;
use Facile\MongoDbBundle\Services\Loggers\NullLogger;

/**
 * Class LoggerFactory.
 */
class LoggerFactory
{
    /**
     * @var string
     */
    private $environment;

    /**
     * LoggerFactory constructor.
     *
     * @param string $environment
     */
    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return DataCollectorLoggerInterface
     */
    public function createLogger(): DataCollectorLoggerInterface
    {
        if ($this->environment === 'dev') {
            return new MongoLogger();
        }

        return new NullLogger();
    }
}
