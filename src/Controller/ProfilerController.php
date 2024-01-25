<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Controller;

use Facile\MongoDbBundle\DataCollector\MongoDbDataCollector;
use Facile\MongoDbBundle\DataCollector\MongoQuerySerializer;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Profiler\Profiler;

class ProfilerController implements ContainerAwareInterface
{
    /** @var Container */
    private $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function explainAction($token, $queryNumber)
    {
        /** @var Profiler $profiler */
        $profiler = $this->container->get('profiler');
        $profiler->disable();

        $profile = $profiler->loadProfile($token);
        /** @var MongoDbDataCollector $dataCollector */
        $dataCollector = $profile->getCollector('mongodb');
        $queries = $dataCollector->getQueries();

        $query = $queries[$queryNumber];

        $query->setFilters($this->walkAndConvertToUTCDatetime($query->getFilters()));

        $service = $this->container->get('mongo.explain_query_service');

        try {
            $result = $service->execute($query);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'err' => $e->getMessage(),
            ]);
        }

        return new JsonResponse(MongoQuerySerializer::prepareItemData($result->toArray()));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function walkAndConvertToUTCDatetime($data)
    {
        if (! \is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $item) {
            if (\is_string($item) && 0 === strpos($item, 'ISODate')) {
                $time = str_replace(['ISODate("', '")'], '', $item);
                $dateTime = new \DateTime($time);
                $item = new UTCDatetime($dateTime->getTimestamp() * 1_000);
            }

            $data[$key] = $this->walkAndConvertToUTCDatetime($item);
        }

        return $data;
    }
}
