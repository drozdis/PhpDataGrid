<?php
namespace Widget\Grid;

/**
 * Class AbstractGridCreator
 */
abstract class AbstractGridCreator
{
    /**
     * @return array
     */
    public function getDefaults()
    {
        return array();
    }

    /**
     * @param GridBuilder $builder
     */
    abstract public function buildGrid(GridBuilder $builder);
}
