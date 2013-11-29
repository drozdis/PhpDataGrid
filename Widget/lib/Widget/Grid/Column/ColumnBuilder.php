<?php
namespace Widget\Grid\Column;

use Widget\Helper;

/**
 * Column builder
 */
class ColumnBuilder
{
    /**
     * @var \Widget\Grid\Column\Column
     */
    private $column;

    /**
     * @return \Widget\Grid\Column\Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param \Widget\Grid\Column\Column | string
     * @param array $options
     */
    public function __construct($type, $options = array())
    {
        if (is_object($type)) {
            $this->column = $type;
        } elseif (is_string($type)) {
            if (class_exists($type)) {
                $class = $type;
            } elseif (class_exists('\Widget\Grid\Column\\' . ucfirst($type))) {
                $class = '\Widget\Grid\Column\\' . ucfirst($type);
            } else {
                throw new \Exception('Unknown class ' . $type);
            }
            $this->column = new $class();
        } else {
            throw new \Exception('Unknown configuration');
        }

        //apply options
        \Widget\Helper::setConstructorOptions($this->column, $options);
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @return object
     * @throws \Exception
     */
    public function setFilter($type = 'text', $options = array())
    {
        if (is_object($type)) {
            $filter = $type;
        } elseif (is_string($type)) {
            if (class_exists($type)) {
                $class = $type;
            } elseif (class_exists('\Widget\Grid\Filter\\' . ucfirst($type) . 'Filter')) {
                $class = '\Widget\Grid\Filter\\' . ucfirst($type) . 'Filter';
            } else {
                throw new \Exception('Unknown class ' . $type);
            }
            $filter = new $class();
        } else {
            throw new \Exception('Unknown configuration');
        }

        //apply options
        \Widget\Helper::setConstructorOptions($filter, $options);

        //set filter
        $this->column->setFilter($filter);
    }
}
