<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Да/Нет)
 * @package Widget\Grid\Filter
 * @author drozd
 */
class BooleanFilter extends SelectFilter
{	
	/**
	 * @var Array
	 */	
	protected $_options = array(1 => 'Да', 0 => 'Нет');
}