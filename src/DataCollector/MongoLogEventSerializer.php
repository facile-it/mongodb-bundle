<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\DataCollector;

use Facile\MongoDbBundle\Models\LogEvent;

/**
 * Class MongoLogEventSerializer
 */
class MongoLogEventSerializer
{
    /**
     * @param LogEvent $event
     */
    public static function serialize(LogEvent $event)
    {
        $event->setFilters(self::prepareUnserializableData($event->getFilters()));
        $event->setData(self::prepareUnserializableData($event->getData()));
        $event->setOptions(self::prepareUnserializableData($event->getOptions()));
    }

    /**
     * @param array|object $data
     *
     * @return array|object
     */
    private static function prepareUnserializableData($data)
    {
        foreach ($data as $key => $item) {
            if (method_exists($item, 'getArrayCopy')) {
                $data[$key] = self::prepareUnserializableData($item->getArrayCopy());
            }

            if (method_exists($item, 'toDateTime')) {
                $data[$key] = $item->toDateTime()->format('r');
                continue;
            }

            if (method_exists($item, '__toString')) {
                $data[$key] = $item->__toString();
                continue;
            }

            if (is_array($item) || is_object($item)) {
                $data[$key] = self::prepareUnserializableData((array)$item);
            }
        }

        return $data;
    }

}
