<?php
namespace Widget\Grid\Column;

/**
 * Колонка изображение
 * @package Widget\Grid\Column
 */
class Image extends Column
{		
	/**
	 * @var Array
	 */
	protected $_decorators = array('resize'=>array('width' => 50, 'height' => 50));
	
	/**
	 * @param Array $decorators
	 * @return Image
	 */
	public function setDecorators($decorators)
	{
		$this->_decorators = $decorators;
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
    protected function _value($row)
    {  	
    	$value = parent::_value($row);
    	if (!empty($value)) {
    		return $this->getView()->image($value, $this->_decorators);
    	}
    	return '';  	
    }
}