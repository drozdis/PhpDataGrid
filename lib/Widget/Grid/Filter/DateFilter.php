<?php
namespace Widget\Grid\Filter;
use Widget\Grid\Storage\AbstractStorage;

/**
 * Клас фильтра колонки дат
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class DateFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $column = $this->getColumn()->getName();
        $grid = $this->getGrid();
        $value = $this->getValue();

        $attribs = array(
            'class' => 'input-text',
            'onkeypress' => $grid->getJavascriptObject() . '.doFilterEnter(event);'
        );

        $html = '<div class="filter-date">';
        $html .= $this->getView()->calendar($column, $value, array('button' => true), $attribs);
        $html .= '</div>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(AbstractStorage $store)
    {
        $value = $this->getValue();
        if (!empty($value)) {
            $store->addFilter($this->getColumn()->getName(), $this->getField(), $value, ' = ?');
        }

        return $this;
    }
}
