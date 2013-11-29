<?php
namespace Widget\Grid;

/**
 * Class GridFactory
 */
class GridFactory
{
    /**
     * @param string $type
     *
     * @return Grid
     * @throws \Exception
     */
    public static function create($type)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($type);
        if (!class_exists($class)) {
            throw new \Exception('Unknown type ' . $type);
        }

        return new $class;
    }

    /**
     * @param string|Grid $type
     * @param array       $options
     *
     * @return GridBuilder
     */
    public static function createBuilder($type)
    {
        if (!$type instanceof Grid) {
            $grid = self::create($type);
        }

        return new GridBuilder($grid);
    }

    /**
     * @param AbstractGridCreator $creator
     * @param string|Grid         $type
     * @param array               $options
     *
     * @return Grid
     */
    public static function createGrid(AbstractGridCreator $creator, $type, $options = array())
    {
        //create builder
        $builder = self::createBuilder($type);

        //build bgrid
        $creator->buildGrid($builder, $creator->getDefaults() + $options);

        //get grid
        return $builder->getGrid();
    }

}
