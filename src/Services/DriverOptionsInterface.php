<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

/**
 * Interface DriverOptionsInterface
 */
interface DriverOptionsInterface
{
    /**
     * @param array $configuration
     */
    public function buildDriverOptions(array $configuration): array;

    /**
     * @param array $contextOptions
     */
    public function buildContext(array $contextOptions);
}
