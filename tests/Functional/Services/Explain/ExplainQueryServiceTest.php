<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Services\Explain;

use Facile\MongoDbBundle\Models\Query;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;

class ExplainQueryServiceTest extends AppTestCase
{
    protected function setUp(): void
    {
        $this->setEnvDev();
        parent::setUp();
    }

    public function test_execute(): void
    {
        $query = new Query();
        $query->setMethod('findOne');
        $query->setFilters(['_id' => 1]);
        $query->setClient('test_client');
        $query->setDatabase('testFunctionaldb');

        $service = $this->getContainer()->get('mongo.explain_query_service');
        $explain = $service->execute($query)->toArray();

        $this->assertNotEmpty($explain);
    }

    public function test_execute_not_available_method(): void
    {
        $query = new Query();
        $query->setMethod('fooooo');
        $query->setFilters(['_id' => 1]);
        $query->setClient('test_client');
        $query->setDatabase('testFunctionaldb');

        $service = $this->getContainer()->get('mongo.explain_query_service');
        $this->expectException(\InvalidArgumentException::class);
        $service->execute($query);
    }
}
