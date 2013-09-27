<?php
namespace Widget\Grid\Column;

/**
 * Column yes/no
 * @package Widget\Grid\Column
 */
class Boolean extends Column
{		
	/**
	 * @inheritdoc
	 */
    protected function _value($row)
    {  	
    	return parent::_value($row) > 0 ? 'да' : 'нет';  	
    }
}