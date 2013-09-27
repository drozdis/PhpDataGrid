<?php
namespace Widget\Grid\Column;

/**
 * Column checkbox
 * @package Widget\Grid\Column
 */
class Checkbox extends Column
{		
	/**
	 * Обрабочик чекбокса 
	 * @var String
	 */
	protected $_handler = '';
	
	/**
	 * Название поля
	 * @var String
	 */
	protected $_inputName = '';
	
	/**
	 * Выкл чекбоксы
	 * @var Array
	 */
	protected $_disabled = array();
	
	/**
	 * @return String
	 */
	public function getHandler() 
	{
		return $this->_handler;
	}
	
	/**
	 * @param String $handler
	 * @return Checkbox
	 */
	public function setHandler($handler) 
	{
		$this->_handler = $handler;
		return $this;
	}
	
	/**
	 * @param Array $disabled
	 * @return Checkbox
	 */
	public function setDisabled($disabled)
	{
		$this->_disabled = $disabled;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getInputName()
	{
		return $this->_inputName ? $this->_inputName : $this->getName();
	}
	
	/**
	 * @param String $inputName
	 * @return Checkbox
	 */
	public function setInputName($inputName)
	{
		$this->_inputName = $inputName;
		return $this;
	}

    /**
     * @inheritdoc
     */
    protected function _value($row)
    {  	
    	$idField = $this->getGrid()->getStorage()->getIdField();
    	$checked = parent::_value($row) > 0 ? true : false; 
		$disabled = in_array($row[$idField], $this->_disabled);
    	$html = '<input type="checkbox" name="'.$this->getInputName().'" value="'.$row[$idField].'" '.($checked ? 'checked="checked"':'').' '.($disabled ? 'disabled="disabled"':'').' '.($this->getHandler() ? 'onchange="'.$this->getHandler().'"':'').'  />';
    	if ($disabled) {
    		$html .= '<input type="hidden" name="'.$this->getInputName().'" value="'.$row[$idField].'" />';
    	}
    	return $html;
    }
}