<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Fixtures;

final class FixtureSorter
{
    public static function sort(array $fixtures): array
    {
        usort($fixtures, self::orderedFixtureSorter());

        return $fixtures;
    }

    private static function orderedFixtureSorter(): \Closure
    {
        return static function ($a, $b): int {
            if ($a instanceof OrderedFixtureInterface && $b instanceof OrderedFixtureInterface) {
                return $a->getOrder() - $b->getOrder();
            }

            if ($a instanceof OrderedFixtureInterface && ! $b instanceof OrderedFixtureInterface) {
                return 1;
            }

            if (! $a instanceof OrderedFixtureInterface && $b instanceof OrderedFixtureInterface) {
                return -1;
            }

            return 0;
        };
    }
}
