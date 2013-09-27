<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Выпадающий список - данные берутся из модели)
 *
 * @package Widget\Grid\Filter
 * @author drozd
 */
class SelectModelFilter extends SelectFilter
{
	/**
	 * @var String
	 */
	protected $_idField = '';

	/**
	 * @var String
	 */
	protected $_titleField = '';

	/**
	 * @var String|A1_Model_Entity_Abstract
	 */
	protected $_model = '';

	/**
	 * @var A1_Widget_Grid_Storage_Abstract
	 */
	protected $_store = null;

	/**
	 * Зависит ли список от данных в сторе или нет (true - не зависит)
	 * @var Boolean
	 */
	protected $_full = false;

	/**
	 * Тип фильтра
	 * @var String
	 */
	protected $_type = 'integer';

	/**
	 * @return Boolean
	 */
	public function getFull()
	{
		return $this->_full;
	}

	/**
	 * @param Boolean $full
	 * @return A1_Widget_Grid_Filter_SelectModel
	 */
	public function setFull($full)
	{
		$this->_full = $full;
		return $this;
	}

	/**
	 * @param String $idField
	 * @return A1_Widget_Grid_Filter_Select
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
		return $this->_idField ? $this->_idField : A1_Core::model($this->getModel())->getIdField();;
	}

	/**
	 * @param String $titleField
	 * @return A1_Widget_Grid_Filter_Select
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
		return $this->_titleField ? $this->_titleField : A1_Core::model($this->getModel())->getTitleField();
	}

	/**
	 * @param String|A1_Model_Entity_Abstract $model
	 * @return A1_Widget_Grid_Filter_SelectModel
	 */
	public function setModel($model)
	{
		$this->_model = $model;
		return $this;
	}

	/**
	 * @return A1_Model_Entity_Abstract
	 */
	public function getModel()
	{
		return $this->_model;
	}

	/**
	 * (non-PHPdoc)
	 * @see A1_Widget_Grid_Filter_Abstract::apply()
	 */
	public function apply(A1_Widget_Grid_Storage_Abstract $store)
	{
		$this->_store = clone $store;
		return parent::apply($store);
	}

	/**
     * (non-PHPdoc)
     * @see A1_Widget_Grid_Filter_Abstract::render()
     */
	public function render()
    {
    	if (!empty($this->_store)) {
    		if ($this->getFull() === false) {
	    		foreach ($this->getGrid()->getFilters()->getFilters() as $name => $filter) {
	    			if (!$filter->getColumn()) {
	    				throw new Zend_Exception('Поле для фильтрации не существует:'.$name);
	    			}
		    		if ($filter->getColumn()->getName() != $this->getColumn()->getName() && $filter->getValue()) {
		    			$filter->apply($this->_store);
		    		}
		    	}

		    	$this->_store->filter();
				$this->_values = $this->_store->getModel()->getValue($this->getField());
		    	$this->_values = A1_Helper_Array::normalize($this->_values);

		    	if (empty($this->_values)) {
		    		$this->_values = array($this->getValue());
		    	}
    		}

	    	$model = A1_Core::model($this->getModel()); /* @var $model A1_Model_Entity_Abstract */
	    	if (empty($model)) {
	    		throw new Zend_Exception('Модель не найдена: '.$this->getModel());
	    	}
	    	$model -> selColumns();
	    	$model -> selOrder();
	    	$this -> getFull() === false && $model -> setId($this->_values);
            $options = $model -> toData();
	    	$this -> setOptions(A1_Helper_Array::makePairs($options, $this->getIdField(), $this->getTitleField()));
    	}
    	return parent::render();
    }
}