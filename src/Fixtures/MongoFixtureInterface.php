<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

/**
 * Interface MongoFixtureInterface
 */
interface MongoFixtureInterface
{
    /**
     * @return void
     */
    public function loadData();

    /**
     * @return void
     */
    public function loadIndexes();

    /**
     * @return void
     */
    public function collection(): string;
}
