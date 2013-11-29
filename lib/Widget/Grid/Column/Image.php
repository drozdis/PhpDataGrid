<?php
namespace Widget\Grid\Column;

/**
 * Image column
 */
class Image extends Column
{
    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        $value = parent::value($row);
        if (!empty($value)) {
            return '<img src="'.$value.'" />';
        }
        return '';
    }
}
