<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Выпадающий список)
 * 
 * @package A1_Widget
 * @author drozd
 */
class SelectFilter extends TextFilter
{	
	/**
	 * @var Array
	 */	
	protected $_options = array();
	 
	/**
	 * @var Boolean
	 */
	protected $_multiselect = false;

    /**
     * @var bool
     */
    protected $_empty = true;
	
	/**
	 * @return Boolean
	 */
	public function getMultiselect()
	{
		return $this->_multiselect;
	}

	/**
	 * @param Boolean $multiselect
	 * @return SelectFilter
	 */
	public function setMultiselect($multiselect)
	{
		$this->_multiselect = $multiselect;
        return $this;
	}

    /**
     * @param Boolean $multiselect
     * @return SelectFilter
     */
    public function setEmpty($empty)
    {
        $this->_empty = $empty;
        return $this;
    }

	/**
	 * @param Array $options
	 * @return SelectFilter
	 */
	public function setOptions($options)
	{
		$this->_options = $options;
		return $this;
	}
	
	/**
	 * @return Array
	 */
	public function getOptions()
	{
		return $this->_options;
	}
    
    /**
     * @inheritdoc
     */
    public function render()
    {
    	$column   = $this->getColumn()->getName();
    	$grid     = $this->getGrid();
    	$options  = $this->getOptions();
    	 
    	if ($this->getMultiselect()) {
    		$html  = '<div class="field-100"><div class="multiselect nowrap" style="height:60px;">';
    		foreach ($options as $key=>$value) {
    			$html .= '<label class="checkbox"><input name="'.$column.'[]" onclick="'.$grid->getJavascriptObject().'.doFilter();" type="checkbox" id="'.$column.$key.'" value="'.$key.'" '.($this->getValue() !== null && $this->getValue() !== '' && in_array($key, (array)$this->getValue()) ? 'checked="checked"': '').' /> '.$value.'</label>';
    		}
    		$html .= '</div></div>';
    	} else {
    		$html  = '<div class="field-100">';
    		$html .= '<select name="'.$column.'" onchange="'.$grid->getJavascriptObject().'.doFilter();">';
    		$this->_empty && $html .= '<option value=""></option>';
    		foreach ($options as $key=>$value) {
    			$html .= '<option value="'.$key.'" '.($this->getValue() !== null && $this->getValue() !== '' && in_array($key, (array)$this->getValue()) ? 'selected="selected"': '').'>'.$value.'</option>';
    		}
    		$html .= '</select>';
    		$html .= '</div>';
    	}
    
    	return $html;
    }
}