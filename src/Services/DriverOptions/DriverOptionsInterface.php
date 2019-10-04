<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\DriverOptions;

/**
 * Interface DriverOptionsInterface
 */
interface DriverOptionsInterface
{
    public function buildDriverOptions(array $configuration): array;
}
