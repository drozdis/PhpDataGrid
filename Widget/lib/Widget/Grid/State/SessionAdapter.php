<?php
namespace Widget\Grid\State;

/**
 * Клас "Сохранение состояния в сессии"
 *
 * @package Widget\Grid\State
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class SessionAdapter extends AbstractAdapter
{
    /**
     * @var array
     */
    protected $store = null;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->store = & $_SESSION[$this->getName()];
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->store['state'];
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->store['state'] = $state;

        return $this;
    }
}
