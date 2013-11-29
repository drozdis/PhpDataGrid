<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Да/Нет)
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class BooleanFilter extends SelectFilter
{
    /**
     * @var array
     */
    protected $options = array(1 => 'Да', 0 => 'Нет');
}
