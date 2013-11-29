<?php
namespace Widget;

/**
 * Class ObserverAbstract
 */
class ObserverAbstract
{
    /**
     * @var ObserverListener[]
     */
    protected $listeners = array();

    /**
     * @return ObserverAbstract[]
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param ObserverListener[] $listeners
     *
     * @return ObserverAbstract
     */
    public function setListeners($listeners)
    {
        $this->listeners = $listeners;

        return $this;
    }

    /**
     * Сгенерировать событие
     * @param string $name
     * @param array  $params
     */
    public function fireEvent($name, $params = array())
    {
        if (!empty($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $listener) {
                /* @var $listener ObserverListener */
                call_user_func_array($listener->getMethod(), $params);
            }
        }
    }

    /**
     * Подписатся на событие
     * @param string           $name
     * @param ObserverListener $listener
     */
    public function on($name, ObserverListener $listener)
    {
        if (empty($this->listeners[$name])) {
            $this->listeners[$name] = array();
        }
        $this->listeners[$name][] = $listener;
    }
}
