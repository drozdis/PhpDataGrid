<?php
namespace Widget;

/**
 * Class ObserverAbstract
 * @package Widget
 */
class ObserverAbstract
{	
	/**
	 * @var ObserverAbstract[]
	 */
	protected $_listeners = array();
				
	/**
	 * @param array $options
	 */
	public function __construct($options = array())
	{
		Helper::setConstructorOptions($this, $options);
		$this->_init();
	}
    
	/**
     * @return ObserverAbstract[]
     */
    public function getListeners()
    {
    	return $this->_listeners;
    }
    
	/**
     * @param ObserverListener[] $listeners
     * @return ObserverAbstract
     */
    public function setListeners($listeners)
    {
    	$this->_listeners = $listeners;
    	return $this;
    }
    
    /**
     * Сгенерировать событие
     * @param String $name
     * @param Array $params
     */
	public function fireEvent($name, $params = array())
	{
		if (!empty($this->_listeners[$name])) {
			foreach ($this->_listeners[$name] as $listener) {
				call_user_func_array($listener->getMethod(), $params + $listener->getParams());
			}
		}
	}

	/**
	 * Подписатся на событие
	 * @param String $name
	 * @param ObserverListener $listener
	 */
	public function on($name, ObserverListener $listener)
	{
		if (empty($this->_listeners[$name])) {
			$this->_listeners[$name] = array();
		}
		$this->_listeners[$name][] = $listener;
	}
}