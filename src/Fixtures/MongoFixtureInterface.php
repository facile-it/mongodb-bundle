<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

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

    public function collection(): string;
}
