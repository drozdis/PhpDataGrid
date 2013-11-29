<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Выпадающий список)
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class SelectRangeFilter extends SelectFilter
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
        $value = $this->getValue();
        if (!empty($value)) {
            $from = isset($value['from']) ? $value['from'] : '';
            $to = isset($value['to']) ? $value['to'] : '';

            $from != '' && $store->addFilter($this->getColumn()->getName() . '_from', $this->getField(), $from, ' >= ?');
            $to != '' && $store->addFilter($this->getColumn()->getName() . '_to', $this->getField(), $to, ' <= ?');
        }

        return $this;
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
                $this->values = A1_Helper_Array::normalize($this->store->getModel()->getValue($this->getField()));

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
            $this->setOptions(A1_Helper_Array::makePairs($model->toData(), $this->getIdField(), $this->getTitleField()));
        }

        $column = $this->getColumn()->getName();
        $grid = $this->getGrid();
        $value = $this->getValue();
        $from = isset($value['from']) ? $value['from'] : '';
        $to = isset($value['to']) ? $value['to'] : '';

        $html = '<div class="filter-container">';
        $html .= '<div class="range">';

        $html .= '<div class="range-line">';
        $html .= 'с:&nbsp;<select class="form-control" name="' . $column . '[from]" onchange="' . $grid->getJavascriptObject() . '.doFilter();" class="input-text select-range">';
        $this->empty && $html .= '<option value=""></option>';
        foreach ($this->getOptions() as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ($from !== null && $from !== '' && in_array($key, (array) $from) ? 'selected="selected"' : '') . '>' . $value . '</option>';
        }
        $html .= '</select>';
        $html .= '</div>';

        $html .= '<div class="range-line">';
        $html .= 'по:&nbsp;<select class="form-control" name="' . $column . '[to]" onchange="' . $grid->getJavascriptObject() . '.doFilter();" class="input-text select-range">';
        $this->empty && $html .= '<option value=""></option>';
        foreach ($this->getOptions() as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ($to !== null && $to !== '' && in_array($key, (array) $to) ? 'selected="selected"' : '') . '>' . $value . '</option>';
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
