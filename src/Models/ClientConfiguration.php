<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Models;

/**
 * Class ClientConfiguration.
 */
class ClientConfiguration
{
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /**
     * @var array
     */
    private $options;

    /**
     * ClientConfiguration constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     * @param array  $options
     */
    public function __construct(
        string $host,
        int $port,
        string $username = '',
        string $password = '',
        array $options = []
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->cleanOptions(array_merge(
            [
                'username' => $this->username,
                'password' => $this->password,
            ],
            $this->options
        ));
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function cleanOptions(array $options): array
    {
        return array_filter($options, function ($value) {
            return !empty($value) || is_int($value) || is_bool($value) || is_float($value);
        });
    }
}
