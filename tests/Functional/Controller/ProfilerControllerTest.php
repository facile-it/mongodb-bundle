<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Controller;

use Facile\MongoDbBundle\Controller\ProfilerController;
use Facile\MongoDbBundle\DataCollector\MongoDbDataCollector;
use Facile\MongoDbBundle\Models\Query;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\HttpKernel\Profiler\Profiler;

class ProfilerControllerTest extends AppTestCase
{
    protected function setUp()
    {
        $this->setEnvDev();
        parent::setUp();
    }

    public function test_explainAction()
    {
        $query = new Query();
        $query->setClient('test_client');
        $query->setDatabase('testFunctionaldb');
        $query->setCollection('fooCollection');
        $query->setMethod('count');
        $query->setFilters(['date' => new UTCDateTime((new \DateTime())->getTimestamp() * 1000)]);

        $collector = $this->prophesize(MongoDbDataCollector::class);
        $collector->getQueries()->shouldBeCalledTimes(1)->willReturn([$query]);

        $profile = $this->prophesize(Profile::class);
        $profile->getCollector('mongodb')->shouldBeCalledTimes(1)->willReturn($collector->reveal());

        $profiler = $this->prophesize(Profiler::class);
        $profiler->loadProfile('fooToken')->shouldBeCalledTimes(1)->willReturn($profile->reveal());
        $profiler->disable()->shouldBeCalledTimes(1);

        $explainService = $this->getContainer()->get('mongo.explain_query_service');

        $container = $this->prophesize(Container::class);
        $container->get('profiler')->willReturn($profiler->reveal());
        $container->get('mongo.explain_query_service')->willReturn($explainService);

        $controller = new ProfilerController();
        $controller->setContainer($container->reveal());

        $response  = $controller->explainAction('fooToken', 0);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());

        $this->assertTrue(is_array($data));
        $this->assertArrayNotHasKey('err', $data);
    }

    public function test_explainAction_error()
    {
        $query = new Query();
        $query->setMethod('fooo');

        $collector = $this->prophesize(MongoDbDataCollector::class);
        $collector->getQueries()->shouldBeCalledTimes(1)->willReturn([$query]);

        $profile = $this->prophesize(Profile::class);
        $profile->getCollector('mongodb')->shouldBeCalledTimes(1)->willReturn($collector->reveal());

        $profiler = $this->prophesize(Profiler::class);
        $profiler->loadProfile('fooToken')->shouldBeCalledTimes(1)->willReturn($profile->reveal());
        $profiler->disable()->shouldBeCalledTimes(1);

        $explainService = $this->getContainer()->get('mongo.explain_query_service');

        $container = $this->prophesize(Container::class);
        $container->get('profiler')->willReturn($profiler->reveal());
        $container->get('mongo.explain_query_service')->willReturn($explainService);

        $controller = new ProfilerController();
        $controller->setContainer($container->reveal());

        $response  = $controller->explainAction('fooToken', 0);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertTrue(is_array($data));
        $this->assertArrayHasKey('err', $data);
    }
}