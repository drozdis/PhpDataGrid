<?php
namespace Widget\Grid\Filter;

/**
 * Class FilterFactory
 */
class FilterFactory
{
    /**
     * @param string $type
     *
     * @return AbstractFilter
     * @throws \Exception
     */
    public static function create($type)
    {
        $class = __NAMESPACE__.'\\'. ucfirst($type).'Filter';
        if (!class_exists($class)) {
            throw new \Exception('Unknown type ' . $type);
        }

        return new $class;
    }
}
