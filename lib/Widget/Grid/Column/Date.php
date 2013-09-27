<?php
namespace Widget\Grid\Column;

/**
 * Колонка дата
 * @package A1_Widget
 * @author drozd
 */
class Date extends Column
{		
	/**
	 * @var String
	 */
	protected $_format = '%d.%m.%Ys';
	
	/**
	 * @param String $format
	 * @return Date
	 */
	public function setFormat($format) 
	{
		$this->_format = $format;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getFormat()
	{
		return $this->_format;
	}
	
	/**
	 * @inheritdoc
	 */
    protected function _value($row)
    {  	
    	$value = parent::_value($row);
    	if ($value != '' && ($value != '0000-00-00' && $value != '01.01.1970' || is_numeric($value) && $value > 0)) {
    		return A1_Core_Date::format($value, $this->getFormat());
    	} 
    	return  '';    	
    }
}