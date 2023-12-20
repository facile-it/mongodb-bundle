<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Explain;

use Facile\MongoDbBundle\Models\Query;

class ExplainCommandBuilder
{
    /**
     * @throws \Exception
     */
    public static function createCommandArgs(
        Query $query,
        string $verbosity = ExplainQueryService::VERBOSITY_ALL_PLAN_EXECUTION
    ): array {
        if ('aggregate' === $query->getMethod()) {
            return [
                'aggregate' => $query->getCollection(),
                'pipeline' => $query->getData(),
                'explain' => true,
            ];
        }

        $args = [
            $query->getMethod() => $query->getCollection(),
        ];

        $args = self::manageCount($query, $args);
        $args = self::manageDistinct($query, $args);
        $args = self::manageFind($query, $args);
        $args = self::manageDelete($query, $args);

        return [
            'explain' => $args,
            'verbosity' => $verbosity,
        ];
    }

    private static function manageCount(Query $query, array $args): array
    {
        if ('count' === $query->getMethod()) {
            $args += [
                'query' => $query->getFilters(),
            ];

            foreach (['limit', 'hint', 'skip'] as $supportedOption) {
                $args += (isset($query->getOptions()[$supportedOption]) ? [$supportedOption => $query->getOptions()[$supportedOption]] : []);
            }
        }

        return $args;
    }

    private static function manageDistinct(Query $query, array $args): array
    {
        if ('distinct' === $query->getMethod()) {
            $args += [
                'key' => $query->getData()['fieldName'],
                'query' => $query->getFilters(),
            ];
        }

        return $args;
    }

    private static function manageFind(Query $query, array $args): array
    {
        if (\in_array($query->getMethod(), ['find', 'findOne', 'findOneAndUpdate', 'findOneAndDelete'])) {
            $args = [
                'find' => $query->getCollection(),
                'filter' => $query->getFilters(),
            ];

            foreach (['sort', 'projection', 'hint', 'skip', 'limit'] as $supportedOption) {
                $args += (isset($query->getOptions()[$supportedOption]) ? [$supportedOption => $query->getOptions()[$supportedOption]] : []);
            }
        }

        return $args;
    }

    private static function manageDelete(Query $query, array $args): array
    {
        if (\in_array($query->getMethod(), ['deleteOne', 'deleteMany'])) {
            return [
                'delete' => $query->getCollection(),
                'deletes' => [
                    ['q' => $query->getFilters(), 'limit' => $query->getOptions()['limit'] ?? 0],
                ],
            ];
        }

        return $args;
    }
}
