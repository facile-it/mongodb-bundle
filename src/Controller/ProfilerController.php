<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Controller;

use Facile\MongoDbBundle\DataCollector\MongoDbDataCollector;
use Facile\MongoDbBundle\DataCollector\MongoQuerySerializer;
use Facile\MongoDbBundle\Services\Explain\ExplainQueryService;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Profiler\Profiler;

class ProfilerController
{
    public function __construct(
        private readonly ExplainQueryService $explain,
        private readonly ?Profiler $profiler
    ) {}

    public function explainAction(string $token, $queryNumber): JsonResponse
    {
        $this->profiler->disable();

        $profile = $this->profiler->loadProfile($token);
        if (! $profile) {
            throw new \RuntimeException('No profile found');
        }

        $dataCollector = $profile->getCollector('mongodb');
        if (! $dataCollector instanceof MongoDbDataCollector) {
            throw new \RuntimeException('MongoDb data collector not found');
        }

        $queries = $dataCollector->getQueries();

        $query = $queries[$queryNumber];

        $query->setFilters($this->walkAndConvertToUTCDatetime($query->getFilters()));

        try {
            $result = $this->explain->execute($query);
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
            if (\is_string($item) && str_starts_with($item, 'ISODate')) {
                $time = str_replace(['ISODate("', '")'], '', $item);
                $dateTime = new \DateTime($time);
                $item = new UTCDatetime($dateTime->getTimestamp() * 1_000);
            }

            $data[$key] = $this->walkAndConvertToUTCDatetime($item);
        }

        return $data;
    }
}
