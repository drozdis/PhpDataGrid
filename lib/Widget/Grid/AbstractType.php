<?php
namespace Widget\Grid;

/**
 * Class AbstractType
 */
abstract class AbstractType
{
    /**
     * @return array
     */
    public function getDefaultsOptions()
    {
        return array();
    }

    /**
     * @param GridBuilder $builder
     */
    abstract public function buildGrid(GridBuilder $builder);
}
