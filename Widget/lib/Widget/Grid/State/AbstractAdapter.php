<?php
namespace Widget\Grid\State;

/**
 * Клас "Адаптер сохранение состояния"
 *
 * @package Widget\Grid\State
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
abstract class AbstractAdapter
{
    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var integer|string
     */
    protected $userId = null;

    /**
     * @var array
     */
    protected $userState = array();

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->init();
    }

    /**
     * initialize adapter
     */
    protected function init()
    {

    }

    /**
     * @param int|string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int|string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return AbstractAdapter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    abstract public function getState();

    /**
     * @param array $state
     *
     * @return AbstractAdapter
     */
    abstract public function setState($save);
}
