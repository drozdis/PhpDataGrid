<?php
namespace Widget\Grid;

/**
 * Class GridFactory
 */
class GridFactory
{
    /**
     * @param string $class
     *
     * @return Grid
     * @throws \Exception
     */
    public static function create($class)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($class);
        if (!class_exists($class)) {
            throw new \Exception('Unknown type ' . $class);
        }

        return new $class;
    }

    /**
     * @param string|Grid $class
     *
     * @return GridBuilder
     */
    public static function createBuilder($class)
    {
        if (!$class instanceof Grid) {
            $grid = self::create($class);
        }

        return new GridBuilder($grid);
    }

    /**
     * @param AbstractType $type
     * @param string|Grid  $class
     * @param array        $options
     *
     * @return Grid
     */
    public static function createGrid(AbstractType $type, $class, $options = array())
    {
        //create builder
        $builder = self::createBuilder($class);

        //build bgrid
        $type->buildGrid($builder, $options);

        //set default options
        $grid = $builder->getGrid();
        \Widget\Helper::setConstructorOptions($grid, $type->getDefaultsOptions());

        //get grid
        return $grid;
    }

}
