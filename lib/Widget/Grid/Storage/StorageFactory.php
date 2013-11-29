<?php
namespace Widget\Grid\Storage;

/**
 * Storage builder
 */
class StorageFactory
{
    /**
     * @param string $type
     *
     * @return AbstractStorage
     * @throws \Exception
     */
    public static function create($type)
    {
        $class = '\\'.__NAMESPACE__.'\\'. ucfirst($type).'Storage';
        if (!class_exists($class)) {
            throw new \Exception('Unknown type ' . $type);
        }

        return new $class;
    }
}
