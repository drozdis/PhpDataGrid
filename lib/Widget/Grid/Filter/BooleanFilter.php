<?php
namespace Widget\Grid\Filter;

/**
 * Yes/No filter
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class BooleanFilter extends SelectFilter
{
    /**
     * @var array
     */
    protected $options = array(1 => 'Yes', 0 => 'No');
}
