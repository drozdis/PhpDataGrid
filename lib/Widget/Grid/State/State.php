<?php
namespace Widget\Grid\State;
use Widget\Grid\Grid;
use Widget\Helper;

/**
 * Клас "Сохранение состояния"
 * 
 * @package Widget\Grid
 * @author drozd
 */
class State
{
	/**
	 * @var Grid
	 */
	protected $_grid = null;
		
	/**
	 * @var AbstractAdapter
	 */
	protected $_adapter = null;

    /**
     * @var integer|string
     */
    protected $_userId = null;

    /**
     * Default state adapter
     * @var string
     */
    protected static $_defaultStateAdapter = 'Widget\Grid\State\SessionAdapter';

    /**
     * @param array $options
     */
    public function __construct($options = array())
	{
		if (empty($options['grid'])) {
			throw new \Exception('Grid is not set');
		}
		Helper::setConstructorOptions($this, $options);

        $adapterOptions = array(
            'name' => $this->getGrid()->getName(),
            'user_id' => $this->getUserId()
        );
		if ($this->getUserId()) {
            $class = self::getDefaultStateAdapter();
            $this->_adapter = new $class($adapterOptions);
		} else {
			$this->_adapter = new SessionAdapter($adapterOptions);
		}
	}
	
	/**
	 * @param Grid $grid
	 * @return State
	 */
	public function setGrid(Grid $grid)
	{
		$this->_grid = $grid;
		return $this;
	}
	
	/**
	 * @return Grid
	 */
	public function getGrid() 
	{
		return $this->_grid;
	}

    /**
     * @param int|string $userId
     * @return State
     */
    public function setUserId($userId)
    {
        $this->_userId = $userId;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getUserId()
    {
        return $this->_userId;
    }

	/**
	 * @return Array
	 */
	public function getState() 
	{
		return $this->_adapter->getState();
	}
	
	/**
	 * @param Array $state
	 * @return State
	 */
	public function setState($state) 
	{
		$this->_adapter->setState($state);
		return $this;
	}

    /**
     * @param string $defaultStateAdapter
     */
    public static function setDefaultStateAdapter($defaultStateAdapter)
    {
        self::$_defaultStateAdapter = $defaultStateAdapter;
    }

    /**
     * @return string
     */
    public static function getDefaultStateAdapter()
    {
        return self::$_defaultStateAdapter;
    }
}