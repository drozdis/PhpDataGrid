<?php
/**
 * Клас фильтра колонки (Выпадающий список - данные берутся из модели)
 *
 * @package A1_Widget
 * @author drozd
 */
class A1_Widget_Grid_Filter_SelectRange extends A1_Widget_Grid_Filter_Select
{
    /**
     * @var String
     */
    protected $_idField = '';

    /**
     * @var String
     */
    protected $_titleField = '';

    /**
     * @var String|A1_Model_Entity_Abstract
     */
    protected $_model = '';

    /**
     * @var A1_Widget_Grid_Storage_Abstract
     */
    protected $_store = null;

    /**
     * Зависит ли список от данных в сторе или нет (true - не зависит)
     * @var Boolean
     */
    protected $_full = false;

    /**
     * @return Boolean
     */
    public function getFull()
    {
        return $this->_full;
    }

    /**
     * @param Boolean $full
     * @return A1_Widget_Grid_Filter_SelectModel
     */
    public function setFull($full)
    {
        $this->_full = $full;
        return $this;
    }

    /**
     * @param String $idField
     * @return A1_Widget_Grid_Filter_Select
     */
    public function setIdField($idField)
    {
        $this->_idField = $idField;
        return $this;
    }

    /**
     * @return String
     */
    public function getIdField()
    {
        return $this->_idField ? $this->_idField : A1_Core::model($this->getModel())->getIdField();;
    }

    /**
     * @param String $titleField
     * @return A1_Widget_Grid_Filter_Select
     */
    public function setTitleField($titleField)
    {
        $this->_titleField = $titleField;
        return $this;
    }

    /**
     * @return String
     */
    public function getTitleField()
    {
        return $this->_titleField ? $this->_titleField : A1_Core::model($this->getModel())->getTitleField();
    }

    /**
     * @param String|A1_Model_Entity_Abstract $model
     * @return A1_Widget_Grid_Filter_SelectModel
     */
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @return A1_Model_Entity_Abstract
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * (non-PHPdoc)
     * @see A1_Widget_Grid_Filter_Abstract::apply()
     */
    public function apply(A1_Widget_Grid_Storage_Abstract $store)
    {
        $this->_store = clone $store;
        $value = $this->getValue();
        if (!empty($value)) {
            $from = isset($value['from']) ? $value['from'] : '';
            $to	  = isset($value['to'])   ? $value['to'] : '';

            $from != '' && $store->addFilter($this->getColumn()->getName().'_from', $this->getField(), $from, ' >= ?');
            $to != ''   && $store->addFilter($this->getColumn()->getName().'_to', $this->getField(), $to, ' <= ?');
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see A1_Widget_Grid_Filter_Abstract::render()
     */
    public function render()
    {
        if (!empty($this->_store)) {
            if ($this->getFull() === false) {
                foreach ($this->getGrid()->getFilters()->getFilters() as $name => $filter) {
                    if (!$filter->getColumn()) {
                        throw new Zend_Exception('Поле для фильтрации не существует:'.$name);
                    }
                    if ($filter->getColumn()->getName() != $this->getColumn()->getName() && $filter->getValue()) {
                        $filter->apply($this->_store);
                    }
                }

                $this->_store->filter();
                $this->_values = A1_Helper_Array::normalize($this->_store->getModel()->getValue($this->getField()));

                if (empty($this->_values)) {
                    $this->_values = array($this->getValue());
                }
            }

            $model = A1_Core::model($this->getModel()); /* @var $model A1_Model_Entity_Abstract */
            if (empty($model)) {
                throw new Zend_Exception('Модель не найдена: '.$this->getModel());
            }
            $model -> selColumns();
            $model -> selOrder();
            $this->getFull() === false && $model -> setId($this->_values);
            $this -> setOptions(A1_Helper_Array::makePairs($model -> toData(), $this->getIdField(), $this->getTitleField()));
        }

        $column = $this->getColumn()->getName();
        $grid   = $this->getGrid();
        $value = $this->getValue();
        $from = isset($value['from']) ? $value['from'] : '';
        $to	  = isset($value['to'])   ? $value['to'] : '';

        $html  = '<div class="filter-container">';
        $html  = '<div class="range">';

        $html .= '<div class="range-line">';
        $html .= 'с:&nbsp;<select name="'.$column.'[from]" onchange="'.$grid->getJavascriptObject().'.doFilter();" class="input-text select-range">';
        $this->_empty && $html .= '<option value=""></option>';
        foreach ($this->getOptions() as $key=>$value) {
            $html .= '<option value="'.$key.'" '.($from !== null && $from !== '' && in_array($key, (array)$from) ? 'selected="selected"': '').'>'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';

        $html .= '<div class="range-line">';
        $html .= 'по:&nbsp;<select name="'.$column.'[to]" onchange="'.$grid->getJavascriptObject().'.doFilter();" class="input-text select-range">';
        $this->_empty && $html .= '<option value=""></option>';
        foreach ($this->getOptions() as $key=>$value) {
            $html .= '<option value="'.$key.'" '.($to !== null && $to !== '' && in_array($key, (array)$to) ? 'selected="selected"': '').'>'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';

        //$html .= '<div class="range-line"><input type="text" name="'.$column.'[from]" value="'.$from.'" class="input-text" placeholder="c" onkeypress="'.$grid->getJavascriptObject().'.doFilterEnter(event);"></div>';
        //$html .= '<div class="range-line"><input type="text" name="'.$column.'[to]" value="'.$to.'" class="input-text" placeholder="по" onkeypress="'.$grid->getJavascriptObject().'.doFilterEnter(event);"></div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;

        return parent::render();
    }
}