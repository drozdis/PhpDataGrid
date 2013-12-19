<?php
namespace Widget\Grid\Filter;

use Widget\Grid\Storage\AbstractStorage;

/**
 * Date filter
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
        $grid   = $this->getGrid();
        $value  = $this->getValue();

        $html = '<input type="text" class="form-control date" value="' . $value . '" id="' . $column . '" name="' . $column . '" />';

        $js = '$(function(){
            $( "#' . $column . '" ).datepicker({
                changeMonth: true,
                dateFormat: "dd.mm.yy",
                onSelect : function() {
                    ' . $grid->getJavascriptObject() . '.doFilter();
                }
            });
        });';

        if ($grid->hasIsAjax()) {
            $html .= '<script type="text/javascript">' . $js . '</script>';
        } else {
            $grid->getResourceManager()->addJavascript($js);
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(AbstractStorage $store)
    {
        $value = $this->getValue();
        if (!empty($value)) {
            $date = new \DateTime($value);

            $store->addFilter($this->getColumn()->getName(), $this->getField(), $date->format('y-m-d'), ' = ?');
        }

        return $this;
    }
}
