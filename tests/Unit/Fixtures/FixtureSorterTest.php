<?php

namespace Facile\MongoDbBundle\Tests\Unit\Fixtures;

use Facile\MongoDbBundle\Fixtures\FixtureSorter;
use Facile\MongoDbBundle\Fixtures\MongoFixtureInterface;
use Facile\MongoDbBundle\Fixtures\OrderedFixtureInterface;
use PHPUnit\Framework\TestCase;

class FixtureSorterTest extends TestCase
{
    public function testSort()
    {
        $toLoad = [
            $this->mockOrdered(2, 'a'),
            $this->mockOrdered(1, 'b'),
            $this->mockUnordered('c'),
            $this->mockOrdered(1, 'd'),
            $this->mockUnordered('e'),
        ];

        $collectionsSorted = implode(
            '',
            array_map(
                static function (MongoFixtureInterface $fixture): string {
                    return $fixture->collection();
                },
                FixtureSorter::sort($toLoad)
            )
        );

        $this->assertContains('a', $collectionsSorted);
        $this->assertContains('b', $collectionsSorted);
        $this->assertContains('c', $collectionsSorted);
        $this->assertContains('d', $collectionsSorted);
        $this->assertContains('e', $collectionsSorted);

        $this->assertIsAfter($collectionsSorted, 'a', ['b', 'c', 'd', 'e']);
        $this->assertIsAfter($collectionsSorted, 'b', ['c', 'e']);
        $this->assertIsAfter($collectionsSorted, 'd', ['c', 'e']);
    }

    private function assertIsAfter(string $collectionsSorted, string $collection, array $others)
    {
        foreach ($others as $other) {
            $this->assertGreaterThan(
                strpos($collectionsSorted, $other),
                strpos($collectionsSorted, $collection)
            );
        }
    }

    private function mockUnordered(string $collectionName): MongoFixtureInterface
    {
        return new class($collectionName) implements MongoFixtureInterface {
            /** @var string */
            private $collectionName;

            public function __construct(string $collectionName)
            {
                $this->collectionName = $collectionName;
            }

            public function loadData()
            {
            }

            public function loadIndexes()
            {
            }

            public function collection(): string
            {
                return $this->collectionName;
            }
        };
    }

    private function mockOrdered(int $order, string $collectionName): OrderedFixtureInterface
    {
        return new class($order, $collectionName) implements MongoFixtureInterface, OrderedFixtureInterface {
            /** @var int */
            private $order;

            /** @var string */
            private $collectionName;

            public function __construct(int $order, string $collectionName)
            {
                $this->order = $order;
                $this->collectionName = $collectionName;
            }

            public function loadData()
            {
            }

            public function loadIndexes()
            {
            }

            public function collection(): string
            {
                return $this->collectionName;
            }

            public function getOrder(): int
            {
                return $this->order;
            }
        };
    }
}
