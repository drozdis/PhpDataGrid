<?php
namespace Widget\Grid\State;
use Widget\Grid\Grid;
use Widget\Helper;

/**
 * Клас "Сохранение состояния"
 *
 * @package Widget\Grid
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class State
{
    /**
     * @var Grid
     */
    protected $grid = null;

    /**
     * @var AbstractAdapter
     */
    protected $adapter = null;

    /**
     * @var integer|string
     */
    protected $userId = null;

    /**
     * Default state adapter
     * @var string
     */
    protected static $defaultStateAdapter = 'Widget\Grid\State\SessionAdapter';

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
            $this->adapter = new $class($this->grid->getName(),$adapterOptions);
        } else {
            $this->adapter = new SessionAdapter($this->grid->getName(), $adapterOptions);
        }
    }

    /**
     * @param Grid $grid
     *
     * @return State
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param int|string $userId
     *
     * @return State
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return array
     */
    public function getState()
    {
        return $this->adapter->getState();
    }

    /**
     * @param array $state
     *
     * @return State
     */
    public function setState($state)
    {
        $this->adapter->setState($state);

        return $this;
    }

    /**
     * @param string $defaultStateAdapter
     */
    public static function setDefaultStateAdapter($defaultStateAdapter)
    {
        self::$defaultStateAdapter = $defaultStateAdapter;
    }

    /**
     * @return string
     */
    public static function getDefaultStateAdapter()
    {
        return self::$defaultStateAdapter;
    }
}
