<?php
namespace Widget\Grid\Filter;

/**
 * Text field
 * 
 * @package Widget\Grid\Filter
 * @author drozd
 */
class TextFilter extends AbstractFilter
{			
   /**
    * @inheritdoc
    */
    public function render()
    {
    	$column = $this->getColumn()->getName();
    	$grid   = $this->getGrid();
    	
    	$html  = '<div class="field-100">';
		$html .= '<input class="input-text no-changes" type="text" name="'.$column.'" value="'.$this->getValue().'" onkeypress="'.$grid->getJavascriptObject().'.doFilterEnter(event);" />';
		$html .= '</div>';
		
		return $html;
    }
}