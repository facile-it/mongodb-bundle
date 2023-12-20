<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class ConnectionEvent.
 *
 * @internal
 */
final class ConnectionEvent extends GenericEvent
{
    public const CLIENT_CREATED = 'facile_mongo_db.event.connection_client.created';

    /**
     * ConnectionEvent constructor.
     */
    public function __construct(string $clientName, array $arguments = [])
    {
        parent::__construct($clientName, $arguments);
    }

    public function getClientName(): string
    {
        return $this->getSubject();
    }
}
