<?php

declare(strict_types = 1);

namespace MongoBundle\Models;

/**
 * Class ConnectionConfiguration
 */
class ConnectionConfiguration
{
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $database;
    /** @var string */
    private $username;
    /** @var string */
    private $password;

    /**
     * ConnectionConfiguration constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, int $port, string $database, string $username, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return bool
     */
    public function hasCredentials(): bool
    {
        return !empty($this->username);
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}