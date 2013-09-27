<?php
namespace Widget\Grid\Column;

/**
 * Button
 *
 * @package Widget\Grid\Column
 */
class Button extends Column
{		
	/**
	 * Обрабочик кнопки
	 * @var String
	 */
	protected $_handler = '';
	
	/**
	 * @var String
	 */
	protected $_type = 'primary';
	
	/**
	 * @return String
	 */
	public function getHandler() 
	{
		return $this->_handler;
	}
	
	/**
	 * @param String $handler
	 * @return A1_Widget_Grid_Column_Checkbox
	 */
	public function setHandler($handler) 
	{
		$this->_handler = $handler;
		return $this;
	}

	/**
	 * @return String
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param string $type
	 * @return A1_Widget_Grid_Column_Checkbox
	 */
	public function setType ($type)
	{
		$this->_type = $type;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see A1_Widget_Grid_Column::_value()
	 */
    protected function _value($row)
    {  	
    	$idField = $this->getGrid()->getStorage()->getIdField();
    	return '<button type="button" class="btn btn-mini btn-'.$this->getType().'" name="'.$this->getName().'" value="'.$row[$idField].'" '.($this->getHandler() ? 'onclick="'.$this->getHandler().'"':'').' >'.$this->getTitle().'</button>';
    }
}