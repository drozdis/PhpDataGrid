<?php
namespace Widget\Grid\Column;

/**
 * Колонка цена
 * @package Widget\Grid\Column;
 * @author drozd
 */
class Price extends Column
{		
	/**
	 * @inheritdoc
	 */
    protected function _value($row)
    {
        return preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/"," ", sprintf('%01.2f', (float) parent::_value($row)));
    }
}