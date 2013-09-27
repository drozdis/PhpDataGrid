<?php
namespace Widget\Grid\Column;

/**
 * Колонка поле ввода
 * @package Widget\Grid\Column
 */
class Input extends Column
{		
	/**
	 * @var String
	 */
	protected $_idField = null;
	
	/**
	 * Атрибуты input
	 * @var Array
	 */
	protected $_attrs = array();
	
	/**
	 * Тип поля ввода name[id] или name_id
	 */
	protected $_type = '[]';
	
	/**
	 * @param String $field
	 * @return Input
	 */
	public function setIdField($field)
	{
		$this->_idField = $field;
		return $this;
	}
	
	/**
	 * @param Array $attrs
	 * @return Input
	 */
	public function setAttrs($attrs)
	{
		$this->_attrs = $attrs;
		return $this;
	}
	
	/**
	 * @param Array $type
	 * @return Input
	 */
	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
    protected function _value($row)
    {  	
    	$value = parent::_value($row);
    	$idField = $this->getGrid()->getStorage()->getIdField();
    	$attrs = array();
    	if (!empty($this->_attrs)) { 
    		foreach ($this->_attrs as $key => $attr) {
    			$attrs[] = $key.'="'.$attr.'"';
    		} 
    	}
    	$attrs = join(' ',$attrs);
    	
    	$name = $this->_type == '[]' ? $this->getName().'['.$row[$this->_idField ? $this->_idField : $idField].']' : $this->getName().'_'.$row[$this->_idField ? $this->_idField : $idField];
    	return '<input '.$attrs.' type="text" class="input-text" name="'.$name.'" value="'.$value.'" />';
    }
}