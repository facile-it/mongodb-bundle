<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Models;

/**
 * Class ClientConfiguration.
 *
 * @internal
 */
final class ClientConfiguration
{
    /** @var string */
    private $uri;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var array */
    private $options;

    /** @var null|string */
    private $authSource;

    /** @var array */
    private $driverOptions;

    /**
     * ClientConfiguration constructor.
     *
     * @param string        $uri
     * @param string        $username
     * @param string        $password
     * @param string|null   $authSource
     * @param array         $options
     * @param array         $driverOptions
     */
    public function __construct(
        string $uri,
        string $username = '',
        string $password = '',
        string $authSource = null,
        array $options = [],
        array $driverOptions = []
    ) {
        $this->uri = $uri;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
        $this->authSource = $authSource;
        $this->driverOptions = $driverOptions;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
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
     * @return null|string
     */
    public function getAuthSource()
    {
        return $this->authSource;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->cleanOptions(
            array_merge(
                [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
                $this->options
            )
        );
    }

    /**
     * @return array
     */
    public function getDriverOptions(): array
    {
        return $this->driverOptions;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function cleanOptions(array $options): array
    {
        return array_filter(
            $options,
            function ($value) {
                return ! empty($value) || \is_int($value) || \is_bool($value) || \is_float($value);
            }
        );
    }
}
