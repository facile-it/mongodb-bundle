<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

/**
 * Interface MongoFixtureInterface.
 */
interface MongoFixtureInterface
{
    /**
     * @return array
     */
    public function loadData();

    /**
     * @return array
     */
    public function loadIndexes();

    /**
     * @return string
     */
    public function collection(): string;
}
