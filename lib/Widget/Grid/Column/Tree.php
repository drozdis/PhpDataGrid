<?php
namespace Widget\Grid\Column;

/**
 * @todo
 * Колонка дерево
 * @package A1_Widget
 * @author drozd
 */
class Tree extends Column
{		
	/**
	 * (non-PHPdoc)
	 * @see A1_Widget_Grid_Column::_value()
	 */
    protected function _value($row)
    {  	
    	return str_repeat('&nbsp;', $this->_level($row[$this->getGrid()->getIdField()])*10).parent::_value($row);		
    }
    
    /**
     * Подсчет уровня вложености
     * @param Integer $id
     * @return Integer $level
     */
    protected function _level($id)
    {
    	static $tree = null;
    	if ($tree === null) {
	    	$rows = $this->getGrid()->getStorage()->getData();
	    	$options = array(
	    		'id'  => $this->getGrid()->getIdField(),
	    		'pid' => $this->getGrid()->getParentField(),
	    		'data' => $rows
	    	);
	    	$tree = new A1_Helper_Tree($options);
    	}
    	return count($tree -> getBranchById($id))-1;
    }
    
}