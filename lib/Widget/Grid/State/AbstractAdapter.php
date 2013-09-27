<?php
namespace Widget\Grid\State;
use Widget\Helper;

/**
 * Клас "Адаптер сохранение состояния"
 * 
 * @package Widget\Grid\State
 * @author drozd
 */
abstract class AbstractAdapter
{	
	/**
	 * @var string
	 */
	protected $_name = null;

    /**
     * @var integer|string
     */
    protected $_userId = null;

    /**
     * @var Array
     */
    protected $_userState = array();

    /**
     * @param array $options
     */
    public function __construct($options = array())
	{
        Helper::setConstructorOptions($this, $options);
		$this->_init();
	}

    /**
     * initialize adapter
     */
    protected function _init()
    {

    }

    /**
     * @param int|string $userId
     */
    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    /**
     * @return int|string
     */
    public function getUserId()
    {
        return $this->_userId;
    }

	/**
	 * @return String
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * @return AbstractAdapter
	 */
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
		
	/**
	 * @return Array
	 */
	abstract public function getState();
		
	/**
	 * @param Array $state
	 * @return AbstractAdapter
	 */
	abstract public function setState($save);	
}