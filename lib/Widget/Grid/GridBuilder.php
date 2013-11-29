<?php
namespace Widget\Grid;

use Widget\AbstractExtension;
use Widget\Grid\Column\Column;
use Widget\Grid\Column\ColumnFactory;
use Widget\Grid\Extension\ExtensionFactory;
use Widget\Grid\Filter\AbstractFilter;
use Widget\Grid\Filter\FilterFactory;
use Widget\Grid\Storage\AbstractStorage;
use Widget\Grid\Storage\StorageFactory;
use Widget\Grid\Toolbar\Toolbar;
use Widget\Grid\Toolbar\ToolbarBuilder;
use Widget\Grid\Toolbar\ToolbarFactory;

/**
 * Builder class for grid
 */
class GridBuilder
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @param Grid  $grid
     * @param array $options
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param AbstractStorage |string $type
     * @param array                   $options
     *
     * @return AbstractStorage
     */
    public function setStorage($type, $options = array())
    {
        if (!$type instanceof AbstractStorage) {
            $type = StorageFactory::create($type);
        }

        //set options
        \Widget\Helper::setConstructorOptions($type, $options);

        //set store
        $this->grid->setStorage($type);

        return $type;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return Column
     */
    public function addColumn($name, $type = 'column', $options = array())
    {
        if (!$type instanceof Column) {
            $type = ColumnFactory::create($type);
        }

        //set options
        \Widget\Helper::setConstructorOptions($type, $options);

        //add to grid
        $this->grid->addColumn($name, $type);

        return $type;
    }

    /**
     * @param AbstractFilter|string $type
     * @param array                 $options
     *
     * @return AbstractFilter
     */
    public function createFilter($type, $options = array())
    {
        if (!$type instanceof AbstractFilter) {
            $type = FilterFactory::create($type);
        }

        //set options
        \Widget\Helper::setConstructorOptions($type, $options);

        return $type;
    }

    /**
     * @param Toolbar|string $type
     * @param array          $options
     *
     * @return ToolbarBuilder
     */
    public function setTopToolbar($type = 'toolbar', $options = array())
    {
        if (!$type instanceof Toolbar) {
            $type = ToolbarFactory::create($type);
        }

        //set options
        \Widget\Helper::setConstructorOptions($type, $options);

        $this->getGrid()->setTopToolbar($type);
    }

    /**
     * @param Toolbar|string $type
     * @param array          $options
     *
     * @return ToolbarBuilder
     */
    public function setBottomToolbar($type = 'toolbar', $options = array())
    {
        if (!$type instanceof Toolbar) {
            $type = ToolbarFactory::create($type);
        }

        //set options
        \Widget\Helper::setConstructorOptions($type, $options);

        $this->getGrid()->setBottomToolbar($type);
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return GridBuilder
     */
    public function addAction($name, $options = array())
    {
        $action = new Action\Action();

        //apply options
        \Widget\Helper::setConstructorOptions($action, $options);

        //set name
        $action->setName($name);

        //add to grid
        $this->grid->addAction($name, $action);

        return $this;
    }

    /**
     * @param AbstractExtension|string $type
     * @param array                    $options
     *
     * @return GridBuilder
     */
    public function addExtension($type, $options = array())
    {
        if (!$type instanceof AbstractExtension) {
            $type = ExtensionFactory::create($type);
        }

        //set options
        $type->setOptions($options);

        //add to grid
        $this->grid->addExtension($type);

        return $this;
    }
}
