<?php
namespace Widget\Grid\Extension\State;

/**
 * Abstract state adapter
 *
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
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
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
     * @param string $name
     *
     * @return $this
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
     * @param array $save
     *
     * @return $this
     */
    abstract public function setState($save);
}
