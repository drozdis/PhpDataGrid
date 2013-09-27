<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Дерево)
 * 
 * @package Widget\Grid\Filter;
 * @author drozd
 */
class TreeFilter extends SelectFilter
{	
	/**
	 * @var String
	 */	
	protected $_idField = 'id';
	
	/**
	 * @var String
	 */	
	protected $_titleField = 'name';
	
	/**
	 * @param String $idField
	 * @return TreeFilter
	 */
	public function setIdField($idField)
	{
		$this->_idField = $idField;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getIdField()
	{
		return $this->_idField;
	}
	
	/**
	 * @param String $titleField
	 * @return TreeFilter
	 */
	public function setTitleField($titleField)
	{
		$this->_titleField = $titleField;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getTitleField()
	{
		return $this->_titleField;
	}

    /**
     * @inheritdoc
     */
	public function render()
    {
    	$column = $this->getColumn()->getName();
    	$grid   = $this->getGrid();
    	$tree   = $this->getOptions();
    	
		$html  = '<div class="field-100">';
		$html .= '<select name="'.$column.'" onchange="'.$grid->getJavascriptObject().'.doFilter();">';
		$html .= '<option value=""></option>';
		if ($tree) {
			$html .= $this->_createTree($tree->data, 0);
		}							
		$html .= '</select>';
		$html .= '</div>';
		
		return $html;
    }
    
	/**
	 * @param Array $data
	 * @return String
	 */
	protected function _createTree($tree, $level = 0)
	{
		$html = '';
		foreach ($tree as $child) {
			$style = '';
			if (!empty($child['child'])) {
				$style = 'style="color:#000;"';
			}
			
			$html .= '<option '.$style.' '.($this->getValue() == $child['data'][$this->_idField] ? 'selected="selected"': '').' value="'.$child['data'][$this->_idField].'">'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level).$child['data'][$this->_titleField].'</option>';
			if (!empty($child['child'])) {
				$html .= $this->_createTree($child['child'], $level+1);
			}
		} 
		return $html;
	}
}