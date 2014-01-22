<?php
namespace Widget\Grid\Extension\State;

/**
 * Session save state adapter
 *
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->store = & $_SESSION[$this->name];
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
