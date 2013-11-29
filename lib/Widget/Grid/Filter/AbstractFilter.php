<?php
namespace Widget\Grid\Filter;

use Widget\Grid\Column\Column;
use Widget\Grid\Storage\AbstractStorage;
use Widget\ObserverAbstract;
use Widget\ObserverListener;
use Widget\RenderInterface;

/**
 * Клас фильтра колонки
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
abstract class AbstractFilter extends ObserverAbstract implements RenderInterface
{
    /**
     * @var Column
     */
    protected $column = null;

    /**
     * Тип фильтра (integer|string)
     * @var string
     */
    protected $type = 'string';

    /**
     * Колонка БД по которой необходимо фильтровать
     * @var string
     */
    protected $field = '';

    /**
     * Значение фильтра
     * @var Mixed
     */
    protected $value = '';

    /**
     * Сохранять состояние фильтра или нет
     * @var Boolean
     */
    protected $state = false;

    /**
     * @return Boolean
     */
    public function isState()
    {
        return $this->state;
    }

    /**
     * @param Boolean $state
     *
     * @return AbstractFilter
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return AbstractFilter
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return !empty($this->field) ? $this->field : $this->getColumn()->getName();
    }

    /**
     * @param string $field
     *
     * @return AbstractFilter
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return Mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Mixed $value
     *
     * @return AbstractFilter
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return AbstractFilter
     */
    public function setColumn(Column $column)
    {
        $filter = $this;
        $listenerLoad = new ObserverListener(function ($store) use ($filter) {
            $filter->apply($store);
        });
        $column->getGrid()->getStorage()->on('before_load', $listenerLoad);

        //set column
        $this->column = $column;

        return $this;
    }

    /**
     * @return \Widget\Grid\Column\Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return \Widget\Grid\Grid
     */
    public function getGrid()
    {
        return $this->getColumn()->getGrid();
    }

    /**
     * @return string
     */
    abstract public function render();

    /**
     * @param AbstractStorage $store
     *
     * @return AbstractFilter
     */
    public function apply(AbstractStorage $store)
    {
        $value = $this->getValue();
        if ($value !== null && $value !== '') {
            switch ($this->getType()) {
                case 'integer':
                    if (is_array($value)) {
                        $value = join(',', $value);
                    }
                    $value = str_replace(array(', ', ' ,'), ',', preg_replace('//\s+//', ' ', $value));
                    $value = array_map('intval', explode(',', str_replace(' ', ',', trim($value))));
                    $store->addFilter($this->getColumn()->getName(), $this->getField(), $value, 'IN (?)');
                    break;

                default:
                    $arr = explode(' ', trim($value));
                    foreach ($arr as &$row) {
                        $store->addFilter($this->getColumn()->getName(), $this->getField(), '%' . $row . '%', 'LIKE LOWER(?)', 'LOWER');
                    }
                    break;
            }
        }

        return $this;
    }
}
