<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\DataCollector;

use Facile\MongoDbBundle\Models\QueryLog;

/**
 * Class MongoLogEventSerializer
 * @internal
 */
class MongoLogEventSerializer
{
    /**
     * @param QueryLog $event
     */
    public static function serialize(QueryLog $event)
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
            $data[$key] = self::prepareItemData($item);
        }

        return $data;
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    private static function prepareItemData($item)
    {
        if (method_exists($item, 'getArrayCopy')) {
            return self::prepareUnserializableData($item->getArrayCopy());
        }

        if (method_exists($item, 'toDateTime')) {
            return $item->toDateTime()->format('r');
        }

        if (method_exists($item, '__toString')) {
            return $item->__toString();
        }

        if (is_array($item) || is_object($item)) {
            return self::prepareUnserializableData((array)$item);
        }

        return $item;
    }

}
