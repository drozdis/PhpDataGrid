<?php
namespace Widget;

/**
 * @package Widget
 * @author drozd
 */
abstract class AbstractDecorator
{		
	/**
	 * @var AbstractWidget
	 */
	protected $_element;

    /**
     * @return String
     */
    abstract public function render($content);
    
   	/**
   	 * @param AbstractWidget $element
     * @return AbstractWidget
   	 */
    public function setElement(AbstractWidget $element)
    {
    	$this->_element = $element;
        return $this;
    }
    
    /**
     * @return AbstractWidget
     */
 	public function getElement()
    {
    	return $this->_element;
    }
}