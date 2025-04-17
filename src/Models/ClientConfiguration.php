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
    public function __construct(
        private readonly string $uri,
        private readonly string $username = '',
        private readonly string $password = '',
        private readonly ?string $authSource = null,
        private readonly array $options = [],
        private readonly array $driverOptions = []
    ) {}

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
