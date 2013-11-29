<?php
namespace Widget\Grid\Toolbar;

/**
 * Toolbar factory
 */
class ToolbarFactory
{
    /**
     * @param string $type
     *
     * @return Column
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
