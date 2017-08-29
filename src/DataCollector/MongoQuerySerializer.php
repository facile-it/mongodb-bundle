<?php declare(strict_types = 1);

namespace Facile\MongoDbBundle\DataCollector;

use Facile\MongoDbBundle\Models\Query;

/**
 * Class MongoQuerySerializer
 * @internal
 */
final class MongoQuerySerializer
{
    /**
     * @param Query $query
     */
    public static function serialize(Query $query)
    {
        $query->setFilters(self::prepareUnserializableData($query->getFilters()));
        $query->setData(self::prepareUnserializableData($query->getData()));
        $query->setOptions(self::prepareUnserializableData($query->getOptions()));
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
    public static function prepareItemData($item)
    {
        if (method_exists($item, 'getArrayCopy')) {
            return self::prepareUnserializableData($item->getArrayCopy());
        }

        if (method_exists($item, 'toDateTime')) {
            return 'ISODate("'.$item->toDateTime()->format('c').'")';
        }

        if (method_exists($item, '__toString')) {
            return $item->__toString();
        }

        if (method_exists($item, 'bsonSerialize')) {
            return $item->bsonSerialize();
        }

        if (is_array($item) || is_object($item)) {
            return self::prepareUnserializableData((array) $item);
        }

        return $item;
    }

}
