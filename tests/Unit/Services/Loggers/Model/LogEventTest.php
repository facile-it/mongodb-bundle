<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\unit\Services\Loggers\Model;

use Facile\MongoDbBundle\Models\Query;
use PHPUnit\Framework\TestCase;

class LogEventTest extends TestCase
{
    public function test_construction()
    {
        $query = new Query();
        $query->setCollection('test_collection');
        $query->setMethod('find');
        $query->setData(['_id'=>'1000000000001']);
        $query->setExecutionTime(1000);
        $query->setClient('test_client');
        $query->setDatabase('test_db');
        $query->setReadPreference('secondaryPreferred');

        $this->assertNotNull($query->getStart());
        $this->assertEquals('test_collection', $query->getCollection());
        $this->assertEquals('find',$query->getMethod());
        $this->assertEquals(1000,$query->getExecutionTime());
        $this->assertEquals(['_id'=>'1000000000001'],$query->getData());
        $this->assertEquals('test_client',$query->getClient());
        $this->assertEquals('test_db',$query->getDatabase());
        $this->assertEquals('secondaryPreferred',$query->getReadPreference());

    }
}
