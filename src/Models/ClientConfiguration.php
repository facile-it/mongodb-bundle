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
    private string $uri;

    private string $username;

    private string $password;

    private array $options;

    private ?string $authSource;

    private array $driverOptions;

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

    public function getAuthSource(): ?string
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

    public function getDriverOptions(): array
    {
        return $this->driverOptions;
    }

    private function cleanOptions(array $options): array
    {
        return array_filter(
            $options,
            fn($value): bool => ! empty($value)
                || \is_int($value)
                || \is_bool($value)
                || \is_float($value)
        );
    }
}
