<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\Services\Explain;

use Facile\MongoDbBundle\Models\Query;
use Facile\MongoDbBundle\Services\Explain\ExplainCommandBuilder;
use Facile\MongoDbBundle\Services\Explain\ExplainQueryService;
use PHPUnit\Framework\TestCase;

class ExplainCommandBuilderTest extends TestCase
{
    public function test_count()
    {
        $query = new Query();
        $query->setCollection('test_collection');
        $query->setMethod('count');
        $query->setFilters(['id' => 1]);

        $args = ExplainCommandBuilder::createCommandArgs($query);

        $this->assertEquals(
            [
                'explain' => [
                    'count' => 'test_collection',
                    'query' => ['id' => 1]
                ],
                'verbosity' => ExplainQueryService::VERBOSITY_ALL_PLAN_EXECUTION,
            ],
            $args
        );
    }

    public function test_distinct()
    {
        $query = new Query();
        $query->setCollection('test_collection');
        $query->setMethod('distinct');
        $query->setFilters(['id' => 1]);
        $query->setData(['fieldName'=>'test']);

        $args = ExplainCommandBuilder::createCommandArgs($query);

        $this->assertEquals(
            [
                'explain' => [
                    'distinct' => $query->getCollection(),
                    'key' => 'test',
                    'query' => $query->getFilters(),
                ],
                'verbosity' => ExplainQueryService::VERBOSITY_ALL_PLAN_EXECUTION,
            ],
            $args
        );
    }

    public function test_aggregate()
    {
        $query = new Query();
        $query->setCollection('test_collection');
        $query->setMethod('aggregate');
        $query->setFilters(['id' => 1]);

        $args = ExplainCommandBuilder::createCommandArgs($query);

        $this->assertEquals(
            [
                'aggregate' => $query->getCollection(),
                'pipeline' => $query->getData(),
                'explain' => true,
            ],
            $args
        );
    }

    /**
     * @dataProvider findsProvider
     *
     * @param string $method
     * @param bool   $projection
     */
    public function test_finds(string $method, bool $projection = false)
    {
        $query = new Query();
        $query->setCollection('test_collection');
        $query->setMethod($method);
        $query->setFilters(['id' => 1]);
        if($projection) {
            $query->setOptions([
                'projection' => '_id',
            ]);
        }


        $args = ExplainCommandBuilder::createCommandArgs($query);

        $expected = [
            'explain' => [
                'find' => 'test_collection',
                'filter' => ['id' => 1]
            ],
            'verbosity' => ExplainQueryService::VERBOSITY_ALL_PLAN_EXECUTION,
        ];

        if($projection) {
            $expected['explain']['projection'] = '_id';
        }

        $this->assertEquals($expected, $args);
    }

    public function findsProvider()
    {
        return [
            ['find', true],
            ['findOne'],
            ['findOneAndUpdate'],
            ['findOneAndDelete'],
        ];
    }

    /**
     * @dataProvider deletedsProvider
     *
     * @param string $method
     * @param int    $limit
     */
    public function test_deletes(string $method, int $limit = 0)
    {
        $query = new Query();
        $query->setCollection('test_collection');
        $query->setMethod($method);
        $query->setFilters(['id' => 1]);
        if($limit) {
            $query->setOptions([
                'limit' => $limit,
            ]);
        }

        $args = ExplainCommandBuilder::createCommandArgs($query);

        $expected = [
            'explain' => [
                'delete' => $query->getCollection(),
                'deletes' => [
                    ['q' => $query->getFilters(), 'limit' => $limit,]
                ]
            ],
            'verbosity' => ExplainQueryService::VERBOSITY_ALL_PLAN_EXECUTION,
        ];

        $this->assertEquals($expected, $args);
    }

    public function deletedsProvider()
    {
        return [
            ['deleteOne', 0],
            ['deleteMany', 4],
        ];
    }
}
