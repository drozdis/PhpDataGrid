<?php
namespace Widget\Grid\Extension;

/**
 * Class ExtensionFactory
 */
class ExtensionFactory
{
    /**
     * @param string $type
     *
     * @return \Widget\AbstractExtension
     * @throws \Exception
     */
    public static function create($type)
    {
        $class = __NAMESPACE__.'\\'. ucfirst($type);
        if (!class_exists($class)) {
            throw new \Exception('Unknown type ' . $type);
        }

        return new $class;
    }
}
