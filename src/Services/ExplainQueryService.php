<?php

namespace Facile\MongoDbBundle\Services;

use Facile\MongoDbBundle\Models\Query;
use InvalidArgumentException;
use MongoDB\Driver\Command;

class ExplainQueryService
{
    const VERBOSITY_QUERY_PLANNER = 'queryPlanner';
    const VERBOSITY_EXECUTION_STATS = 'executionStats';
    const VERBOSITY_ALL_PLAN_EXECUTION = 'allPlansExecution';

    public static $acceptedMethod = ['count', 'distinct', 'group', 'find', 'findAndModify', 'delete', 'update'];

    /** @var ClientRegistry */
    private $clientRegistry;

    /**
     * Constructs a explain command.
     *
     * Supported options:
     * verbosity : queryPlanner | executionStats Mode | allPlansExecution (default)
     * The explain command provides information on the execution of the following commands:
     * count, distinct, group, find, findAndModify, delete, and update.
     *
     * @param ClientRegistry $clientRegistry
     */
    public function __construct(ClientRegistry $clientRegistry)
    {

        $this->clientRegistry = $clientRegistry;
    }

    /**
     * Execute the operation.
     *
     * @param string $connection
     * @param Query $query
     * @param string $verbosity
     * @return array
     * @throws \MongoDB\Driver\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\WriteException
     * @throws \MongoDB\Driver\Exception\WriteConcernException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     * @throws \MongoDB\Driver\Exception\Exception
     * @throws \MongoDB\Driver\Exception\DuplicateKeyException
     * @throws \MongoDB\Driver\Exception\ConnectionException
     * @throws \MongoDB\Driver\Exception\AuthenticationException
     */
    public function execute(string $connection, Query $query, string $verbosity = self::VERBOSITY_ALL_PLAN_EXECUTION)
    {
        if (!in_array($query->getMethod(), self::$acceptedMethod)) {
            throw new InvalidArgumentException(
                'Cannot explain the method'.$query->getMethod().'. Allowed method '.self::$acceptedMethod
            );
        };

        $manager = $this->clientRegistry->getClient('test_client')->getManager();

        return $manager->executeCommand('collaboratori', $this->createCommand($query, $verbosity))->toArray();
    }

    /**
     * Create the explain command.
     *
     * @return Command
     * @throws \MongoDB\Driver\Exception\InvalidArgumentException
     */
    private function createCommand(Query $query, string $verbosity)
    {
        $args = [
            $query->getMethod() => $query->getCollection(),
            'query' => $query->getFilters(),
        ];

        $cmd = [
            'explain' => $args,
            'verbosity' => $verbosity,
        ];

        return new Command($cmd);
    }
}
