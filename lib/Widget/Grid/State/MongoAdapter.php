<?php
namespace Widget\Grid\State;

/**
 * Клас "Сохранение состояния в MongoDb"
 * 
 * @package Widget\Grid\State
 * @author drozd
 */
class MongoAdapter extends AbstractAdapter
{	
	/**
	 * @var \MongoCollection
	 */
	protected $_mongo = null;
	
	/**
	 * Инициализация Mongo
	 */
	protected function _initConnection()
	{
		$m = new MongoClient();
		$this->_mongo = $m->state->widgets;
	}

    /**
     * @inheritdoc
     */
    protected function _init()
    {
        $this->_initConnection();

        $this->_userState = $this->_mongo->findOne(array("_id" => $this->getUserId()));
    }

    /**
     * @inheritdoc
     */
	public function getState() 
	{		
		return !empty($this->_userState[$this->_name]) ? $this->_userState[$this->_name] : array();
	}

    /**
     * @inheritdoc
     */
	public function setState($save) 
	{
		$this->_userState[$this->_name] = $save;
		$this->_userState['_id'] = $this->getUserId();
		
		try {
			$this->_mongo->insert($this->_userState);
		} catch (Exception $e) {
			$this->_mongo->update(array("_id" => $this->getUserId()), $this->_userState);
		}
		return $this;
	}
}