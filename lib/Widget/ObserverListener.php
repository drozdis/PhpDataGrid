<?php
namespace Widget;

/**
 * Observer Event
 * @package A1_Widget
 * @author drozd
 */
class ObserverListener
{	
	/**
	 * Событие
	 * @var String
	 */
	protected $_name = '';
	
	/**
	 * Вызываемый метод
	 * @var Function|Array
	 */
	protected $_method = null;
		
	/**
	 * @var Array
	 */
	protected $_params = array();
	
	/**
	 * @param Array $options
	 */
	public function __construct($options = array())
	{
		Helper::setConstructorOptions($this, $options);
	}
    
	/**
     * @return String
     */
    public function getName()
    {
    	return $this->_name;
    }
    
	/**
     * @param String $name
     * @return ObserverListener
     */
    public function setName($name)
    {
    	$this->_name = $name;
    	return $this;
    }
    
    /**
     * @param Array $params
     * @return ObserverListener
     */
    public function setParams($params)
    {
    	$this->_params = $params;
    	return $this;
    }
    
    /**
     * @return Array
     */
    public function getParams()
    {
    	return $this->_params;
    }

	/**
     * @return Function|Array
     */
    public function getMethod()
    {
    	return $this->_method;
    }
    
	/**
     * @example
     * $grid->getStorage()->on('before_load', new ObserverListener(array('method' => array($this, 'apply'))));
     *
     * or
     *
     * $callback = function($grid) {};
     * $column->on('set_grid', new ObserverListener(array('method' => $callback)));
     *
     * @param Array|Function $name
     * @return ObserverListener
     */
    public function setMethod($name)
    {
    	$this->_method = $name;
    	return $this;
    }
}