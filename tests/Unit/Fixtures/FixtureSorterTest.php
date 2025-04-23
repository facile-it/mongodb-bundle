<?php

namespace Facile\MongoDbBundle\Tests\Unit\Fixtures;

use Facile\MongoDbBundle\Fixtures\FixtureSorter;
use Facile\MongoDbBundle\Fixtures\MongoFixtureInterface;
use Facile\MongoDbBundle\Fixtures\OrderedFixtureInterface;
use PHPUnit\Framework\TestCase;

class FixtureSorterTest extends TestCase
{
    public function testSort(): void
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
                static fn(MongoFixtureInterface $fixture): string => $fixture->collection(),
                FixtureSorter::sort($toLoad)
            )
        );

        $this->assertStringContainsString('a', $collectionsSorted);
        $this->assertStringContainsString('b', $collectionsSorted);
        $this->assertStringContainsString('c', $collectionsSorted);
        $this->assertStringContainsString('d', $collectionsSorted);
        $this->assertStringContainsString('e', $collectionsSorted);

        $this->assertIsAfter($collectionsSorted, 'a', ['b', 'c', 'd', 'e']);
        $this->assertIsAfter($collectionsSorted, 'b', ['c', 'e']);
        $this->assertIsAfter($collectionsSorted, 'd', ['c', 'e']);
    }

    private function assertIsAfter(string $collectionsSorted, string $collection, array $others): void
    {
        foreach ($others as $other) {
            $this->assertGreaterThan(
                strpos($collectionsSorted, (string) $other),
                strpos($collectionsSorted, $collection)
            );
        }
    }

    private function mockUnordered(string $collectionName): MongoFixtureInterface
    {
        return new class ($collectionName) implements MongoFixtureInterface {
            public function __construct(private readonly string $collectionName) {}

            public function loadData(): void {}

            public function loadIndexes(): void {}

            public function collection(): string
            {
                return $this->collectionName;
            }
        };
    }

    private function mockOrdered(int $order, string $collectionName): OrderedFixtureInterface
    {
        return new class ($order, $collectionName) implements MongoFixtureInterface, OrderedFixtureInterface {
            public function __construct(
                private readonly int $order,
                private readonly string $collectionName
            ) {}

            public function loadData(): void {}

            public function loadIndexes(): void {}

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
