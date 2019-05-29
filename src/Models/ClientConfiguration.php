<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Models;

/**
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

    public function __construct(
        string $uri,
        string $username = '',
        string $password = '',
        string $authSource = null,
        array $options = []
    ) {
        $this->uri = $uri;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
        $this->authSource = $authSource;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

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
