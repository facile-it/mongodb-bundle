<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Event;

use Facile\MongoDbBundle\Models\Query;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class QueryEvent.
 * @internal
 */
final class QueryEvent extends GenericEvent
{
    const QUERY_PREPARED = 'facile_mongo_db.event.query_prepared';
    const QUERY_EXECUTED = 'facile_mongo_db.event.query_executed';

    /**
     * QueryEvent constructor.
     *
     * @param Query $queryLog
     * @param array $arguments
     */
    public function __construct(Query $queryLog, array $arguments = [])
    {
        parent::__construct($queryLog, $arguments);
    }

    /**
     * @return Query
     */
    public function getQueryLog(): Query
    {
        return $this->getSubject();
    }
}
