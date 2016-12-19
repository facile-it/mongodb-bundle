<?php

declare(strict_types = 1);

namespace Facile\MongoDbBundle\Event;

use Facile\MongoDbBundle\Models\QueryLog;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class QueryEvent.
 * @internal
 */
class QueryEvent extends GenericEvent
{
    const QUERY_PREPARED = 'facile_mongo_db.event.query_prepared';
    const QUERY_EXECUTED = 'facile_mongo_db.event.query_executed';

    /**
     * QueryEvent constructor.
     *
     * @param QueryLog $queryLog
     * @param array    $arguments
     */
    public function __construct(QueryLog $queryLog, array $arguments = [])
    {
        parent::__construct($queryLog, $arguments);
    }

    /**
     * @return QueryLog
     */
    public function getQueryLog(): QueryLog
    {
        return $this->getSubject();
    }
}
