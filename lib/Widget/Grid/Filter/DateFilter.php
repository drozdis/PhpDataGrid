<?php
namespace Widget\Grid\Filter;
/**
 * @todo render
 * Клас фильтра колонки дат
 * 
 * @package Widget\Grid\Filter
 * @author drozd
 */
class A1_Widget_Grid_Filter_Date extends AbstractFilter
{			
   /**
    * @inheritdoc
    */
    public function render()
    {
    	$column = $this->getColumn()->getName();
    	$grid   = $this->getGrid();
    	$value = $this->getValue();
			
		$attribs = array(
      		'class' => 'input-text',
      		'onkeypress' => $grid->getJavascriptObject().'.doFilterEnter(event);'
      	);
      	
    	$html = '<div class="filter-date">';
    	$html .= $this->getView()->calendar($column, $value, array('button' => true), $attribs);
		$html .= '</div>';
        
		return $html;
    }
    
	/**
	 * (non-PHPdoc)
	 * @see A1_Widget_Grid_Filter_Abstract::apply()
	 */
	public function apply(A1_Widget_Grid_Storage_Abstract $store)
	{
		$value = $this->getValue();
		if (!empty($value)) {
			$store->addFilter($this->getColumn()->getName(), $this->getField(), $value, ' = ?');
		}
		return $this;
	}
}