<?php

namespace Facile\MongoDbBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

/**
 * This class provides the checks needed to avoid the EventDispatcher deprecations from 4.3
 * The Event class has been removed in Symfony 5, so its absence is used as a trigger to stop using the LegacyEventDispatcherProxy.
 */
class EventDispatcherCheck
{
    public static function isPSR14Compliant(): bool
    {
        return ! class_exists(Event::class) || class_exists(LegacyEventDispatcherProxy::class);
    }

    public static function shouldUseLegacyProxy(): bool
    {
        return class_exists(Event::class) && class_exists(LegacyEventDispatcherProxy::class);
    }
}
