<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\DriverOptions;

use MongoDB\Client;

interface DriverOptionsInterface
{
    /**
     * It creates an array of options for constructing a MongoDB\Client.
     *
     * @param array $clientConfiguration client's bundle configuration for which the options are needed
     *
     * @return array Options for MongoDB\Client
     *
     * @see Client
     * @see http://php.net/manual/en/mongodb-driver-manager.construct.php
     */
    public function buildDriverOptions(array $clientConfiguration): array;
}
