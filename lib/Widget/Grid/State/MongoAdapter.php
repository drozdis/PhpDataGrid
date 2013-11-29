<?php
namespace Widget\Grid\State;

/**
 * Клас "Сохранение состояния в MongoDb"
 *
 * @package Widget\Grid\State
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class MongoAdapter extends AbstractAdapter
{
    /**
     * @var \MongoCollection
     */
    protected $mongo = null;

    /**
     * Инициализация Mongo
     */
    protected function initConnection()
    {
        $m = new MongoClient();
        $this->mongo = $m->state->widgets;
    }

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->initConnection();

        $this->userState = $this->mongo->findOne(array("_id" => $this->getUserId()));
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return !empty($this->userState[$this->name]) ? $this->userState[$this->name] : array();
    }

    /**
     * {@inheritdoc}
     */
    public function setState($save)
    {
        $this->userState[$this->name] = $save;
        $this->userState['_id'] = $this->getUserId();

        try {
            $this->mongo->insert($this->userState);
        } catch (Exception $e) {
            $this->mongo->update(array("_id" => $this->getUserId()), $this->userState);
        }

        return $this;
    }
}
