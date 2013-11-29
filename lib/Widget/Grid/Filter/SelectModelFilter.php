<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Выпадающий список - данные берутся из модели)
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 *
 * @todo this filter
 */
class SelectModelFilter extends SelectFilter
{
    /**
     * @var string
     */
    protected $idField = '';

    /**
     * @var string
     */
    protected $titleField = '';

    /**
     * @var string|A1_Model_Entity_Abstract
     */
    protected $model = '';

    /**
     * @var A1_Widget_Grid_Storage_Abstract
     */
    protected $store = null;

    /**
     * Зависит ли список от данных в сторе или нет (true - не зависит)
     * @var Boolean
     */
    protected $full = false;

    /**
     * Тип фильтра
     * @var string
     */
    protected $type = 'integer';

    /**
     * @return Boolean
     */
    public function getFull()
    {
        return $this->full;
    }

    /**
     * @param Boolean $full
     *
     * @return A1_Widget_Grid_Filter_SelectModel
     */
    public function setFull($full)
    {
        $this->full = $full;

        return $this;
    }

    /**
     * @param string $idField
     *
     * @return A1_Widget_Grid_Filter_Select
     */
    public function setIdField($idField)
    {
        $this->idField = $idField;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdField()
    {
        return $this->idField ? $this->idField : A1_Core::model($this->getModel())->getIdField();;
    }

    /**
     * @param string $titleField
     *
     * @return A1_Widget_Grid_Filter_Select
     */
    public function setTitleField($titleField)
    {
        $this->titleField = $titleField;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleField()
    {
        return $this->titleField ? $this->titleField : A1_Core::model($this->getModel())->getTitleField();
    }

    /**
     * @param string|A1_Model_Entity_Abstract $model
     *
     * @return A1_Widget_Grid_Filter_SelectModel
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return A1_Model_Entity_Abstract
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * (non-PHPdoc)
     * @see A1_Widget_Grid_Filter_Abstract::apply()
     */
    public function apply(A1_Widget_Grid_Storage_Abstract $store)
    {
        $this->store = clone $store;

        return parent::apply($store);
    }

    /**
     * (non-PHPdoc)
     * @see A1_Widget_Grid_Filter_Abstract::render()
     */
    public function render()
    {
        if (!empty($this->store)) {
            if ($this->getFull() === false) {
                foreach ($this->getGrid()->getFilters()->getFilters() as $name => $filter) {
                    if (!$filter->getColumn()) {
                        throw new Zend_Exception('Поле для фильтрации не существует:' . $name);
                    }
                    if ($filter->getColumn()->getName() != $this->getColumn()->getName() && $filter->getValue()) {
                        $filter->apply($this->store);
                    }
                }

                $this->store->filter();
                $this->values = $this->store->getModel()->getValue($this->getField());
                $this->values = A1_Helper_Array::normalize($this->values);

                if (empty($this->values)) {
                    $this->values = array($this->getValue());
                }
            }

            $model = A1_Core::model($this->getModel());
            /* @var $model A1_Model_Entity_Abstract */
            if (empty($model)) {
                throw new Zend_Exception('Модель не найдена: ' . $this->getModel());
            }
            $model->selColumns();
            $model->selOrder();
            $this->getFull() === false && $model->setId($this->values);
            $options = $model->toData();
            $this->setOptions(A1_Helper_Array::makePairs($options, $this->getIdField(), $this->getTitleField()));
        }

        return parent::render();
    }
}
