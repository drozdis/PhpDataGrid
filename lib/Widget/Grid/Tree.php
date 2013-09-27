<?php
/**
 * @package A1_Widget
 * @author drozd
 */
class A1_Widget_Grid_Tree extends A1_Widget_Grid
{
	/**
	 * @var String
	 */
	protected $_idField = 'entity_id';
	
	/**
	 * @var String
	 */
	protected $_parentField = 'entity_id';
	
	/**
	 * @return String
	 */
	public function getIdField()
	{
		return $this->_idField;
	}
	
	/**
	 * @param String $idField
	 * @return A1_Widget_Grid_Column_Tree
	 */
	public function setIdField($idField)
	{
		$this->_idField = $idField;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getParentField()
	{
		return $this->_parentField;
	}
	
	/**
	 * @param String $parentField
	 * @return A1_Widget_Grid_Column_Tree
	 */
	public function setParentField($parentField)
	{
		$this->_parentField = $parentField;
		return $this;
	}
	
	/**
	 * Рендеринг данных
	 * @return string
	 */
	protected function _renderBody()
	{
		$html = '';
		$options = array(
			'id'  => $this->getIdField(),
			'pid' => $this->getParentField(),
			'data' => $this->getStorage()->getData()
		);
		
		$tree = new A1_Helper_Tree($options);
		$tree = $tree->tree();
		if (!empty($tree->data)) {
			foreach ($tree->data as $i=>$child) {
				$html .= $this->_renderChilds($child);
			}
		} else {
			$html .= '<tr><td colspan="'.(count($this->_columns)+2).'" style="padding: 10px; text-align: center;">Нет данных</td></tr>';
		}
		return $html;
	}
	
	/**
	 * @param Array $row
	 * @return String
	 */
	protected function _renderChilds($row)
	{
		$html = $this->_renderTr($row['data']);
		if (!empty($row['child'])) {
			foreach ($row['child'] as $child) {
				$html .= $this->_renderChilds($child);
			}
		}
		return $html;
	}
}