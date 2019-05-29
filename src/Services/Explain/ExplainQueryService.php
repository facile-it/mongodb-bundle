<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Services\Explain;

use Facile\MongoDbBundle\Models\Query;
use Facile\MongoDbBundle\Services\ClientRegistry;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;

class ExplainQueryService
{
    const VERBOSITY_QUERY_PLANNER = 'queryPlanner';

    const VERBOSITY_EXECUTION_STATS = 'executionStats';

    const VERBOSITY_ALL_PLAN_EXECUTION = 'allPlansExecution';

    public static $acceptedMethods = [
        'count',
        'distinct',
        'find',
        'findOne',
        'findOneAndUpdate',
        'findOneAndDelete',
        'deleteOne',
        'deleteMany',
        'aggregate',
    ];

    /** @var ClientRegistry */
    private $clientRegistry;

    /**
     * Constructs a explain command.
     *
     * Supported options:
     * verbosity : queryPlanner | executionStats Mode | allPlansExecution (default)
     * The explain command provides information on the execution of the following commands:
     * count, distinct, group, find, findAndModify, delete, and update.
     */
    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    /**
     * @throws \Exception
     */
    public function execute(Query $query, string $verbosity = self::VERBOSITY_ALL_PLAN_EXECUTION): Cursor
    {
        if (! \in_array($query->getMethod(), self::$acceptedMethods)) {
            throw new \InvalidArgumentException(
                'Cannot explain the method \'' . $query->getMethod() . '\'. Allowed methods: ' . implode(', ', self::$acceptedMethods)
            );
        }

        $manager = $this->clientRegistry->getClient($query->getClient(), $query->getDatabase())->__debugInfo()['manager'];

        return $manager
            ->executeCommand(
                $query->getDatabase(),
                new Command(ExplainCommandBuilder::createCommandArgs($query, $verbosity))
            );
    }
}
