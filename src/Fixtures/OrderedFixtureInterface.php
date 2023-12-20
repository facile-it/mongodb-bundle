<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

/**
 * Ordered Fixture interface needs to be implemented by fixtures,
 * which needs to have a specific order.
 *
 * The fixtures without this interface will be loaded after those with it
 *
 * @author Marcin Moskal <moskalmarcin@yahoo.com>
 */
interface OrderedFixtureInterface
{
    public function getOrder(): int;
}
