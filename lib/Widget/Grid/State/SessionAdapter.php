<?php
namespace Widget\Grid\State;

/**
 * Клас "Сохранение состояния в сессии"
 * 
 * @package Widget\Grid\State
 * @author drozd
 */
class SessionAdapter extends AbstractAdapter
{
	/**
	 * @var array
	 */
	protected $_store = null;

    /**
     * @inheritdoc
     */
    protected function _init()
	{
		$this->_store = &$_SESSION[$this->getName()];
	}

    /**
     * @inheritdoc
     */
	public function getState() 
	{
		return $this->_store['state'];
	}

    /**
     * @inheritdoc
     */
    public function setState($state)
	{
		$this->_store['state'] = $state;
		return $this;
	}
}