<?php
namespace Widget\Grid\Filter;

use Widget\Grid\Storage\AbstractStorage;

/**
 * Date range filter
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class DateRangeFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $column = $this->getColumn()->getName();
        $grid   = $this->getGrid();
        $value  = $this->getValue();
        $from   = isset($value['from']) ? $value['from'] : '';
        $to     = isset($value['to']) ? $value['to'] : '';

        $html = '<div class="range">';
        $html .= '<div class="range-line"><input type="text" class="form-control date" value="' . $from . '" placeholder="c" id="' . $column . '_from" name="' . $column . '[from]" /></div>';
        $html .= '<div class="range-line"><input type="text" class="form-control date" value="' . $to . '" placeholder="по" id="' . $column . '_to" name="' . $column . '[to]" /></div>';
        $html .= '</div>';

        $js = '$(function(){
            $( "#' . $column . '_from" ).datepicker({
                changeMonth: true,
                numberOfMonths: 2,
                dateFormat: "dd.mm.yy",
                onSelect : function() {
                    ' . $grid->getJavascriptObject() . '.doFilter();
                },
                onClose: function( selectedDate ) {
                    $( "#' . $column . '_to").datepicker( "option", "minDate", selectedDate );
                }
            });
            $( "#' . $column . '_to" ).datepicker({
                changeMonth: true,
                dateFormat: "dd.mm.yy",
                numberOfMonths: 2,
                onSelect : function() {
                    ' . $grid->getJavascriptObject() . '.doFilter();
                },
                onClose: function( selectedDate ) {
                    $( "#' . $column . '_from" ).datepicker( "option", "maxDate", selectedDate );
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
            $from = isset($value['from']) ? $this->convertDateFormat($value['from']) . ' 0:00:00' : '';
            $to   = isset($value['to']) ? $this->convertDateFormat($value['to']) . ' 23:59:59' : '';

            $from != '' && $store->addFilter($this->getColumn()->getName() . '_from', $this->getField(), $from, ' >= ?');
            $to != '' && $store->addFilter($this->getColumn()->getName() . '_to', $this->getField(), $to, ' <= ?');
        }

        return $this;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function convertDateFormat($value)
    {
        $date = new \DateTime($value);

        return $date->format('y-m-d');
    }
}
