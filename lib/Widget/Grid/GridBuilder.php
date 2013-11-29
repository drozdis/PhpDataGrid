<?php
namespace Widget\Grid;

use Widget\Grid\Grid;
use Widget\Grid\Column\ColumnBuilder;
use Widget\Grid\Storage\StorageBuilder;
use Widget\Grid\Toolbar\ToolbarBuilder;

/**
 * Builder class for grid
 */
class GridBuilder
{
    const ACTION_CLASS = '\Widget\Grid\Action\Action';

    const GRID_CLASS = '\Widget\Grid\Grid';

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param Grid|string $type
     * @param array       $options
     */
    public function __construct($type = self::GRID_CLASS, $options = array())
    {
        $this->options = $options;

        if (is_object($type)) {
            $this->grid = $type;
        } elseif (is_string($type)) {
            if (class_exists($type)) {
                $class = $type;
            } elseif (class_exists('\Widget\Grid\\' . ucfirst($type))) {
                $class = '\Widget\Grid\\' . ucfirst($type);
            } else {
                throw new \Exception('Unknown class ' . $type);
            }
            $this->grid = new $class();
        } else {
            throw new \Exception('Unknown configuration');
        }

        \Widget\Helper::setConstructorOptions($this->grid, $options);
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param \Widget\Grid\Storage\AbstractStorage|string $type
     * @param array                                       $options
     */
    public function setStorage($type, $options = array())
    {
        //create storage builder
        $builder = new StorageBuilder($type, $options);

        //add to grid
        $this->grid->setStorage($builder->getStorage());
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return ColumnBuilder
     */
    public function addColumn($name, $type = 'column', $options = array())
    {
        $builder = new ColumnBuilder($type, $options);

        //add to grid
        $this->grid->addColumn($name, $builder->getColumn());

        return $builder;
    }

    /**
     * @param Toolbar|string $type
     * @param array          $options
     *
     * @return ToolbarBuilder
     */
    public function setTopToolbar($type, $options = array())
    {
        $builder = new ToolbarBuilder($type, $options);
        $toolbar = $builder->getToolbar();

        //add to grid
        $this->grid->setTopToolbar($toolbar);

        return $builder;
    }

    /**
     * @param Toolbar|string $type
     * @param array          $options
     *
     * @return ToolbarBuilder
     */
    public function setBottomToolbar($type, $options = array())
    {
        $builder = new ToolbarBuilder($type, $options);
        $toolbar = $builder->getToolbar();

        //add to grid
        $this->grid->setBottomToolbar($toolbar);

        return $builder;
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return GridBuilder
     */
    public function addAction($name, $options = array())
    {
        $class = self::ACTION_CLASS;
        $action = new $class();

        //apply options
        \Widget\Helper::setConstructorOptions($action, $options);

        //set name
        $action->setName($name);

        //add to grid
        $this->grid->addAction($name, $action);
    }

    /**
     * @param \Widget\AbstractExtension|string $type
     * @param array                            $options
     *
     * @return object
     * @throws \Exception
     *
     * @return GridBuilder
     */
    public function addExtension($type, $options = array())
    {
        if (is_object($type)) {
            $extension = $type;
        } elseif (is_string($type)) {
            if (class_exists($type)) {
                $class = $type;
            } elseif (class_exists('\Widget\Grid\Extension\\' . ucfirst($type))) {
                $class = '\Widget\Grid\Extension\\' . ucfirst($type);
            } else {
                throw new \Exception('Unknown class ' . $type);
            }
            $extension = new $class();
        } else {
            throw new \Exception('Unknown configuration');
        }

        \Widget\Helper::setConstructorOptions($extension, $options);

        //add to grid
        $this->grid->addExtension($extension);
    }
}
