<?php
namespace Widget\Grid\Storage;

/**
 * The storage that provide a loading data dynamically from model
 *
 * @package Widget\Grid\Storage
 */
class ModelStorage extends AbstractStorage
{	
	/**
	 * @var A1_Model_Entity_Abstract
	 */
	protected $_model = null; 
			
	/**
	 * Использовать selWhere модели или нет
	 * @var Boolean
	 */
	protected $_where = true;
	
	/**
	 * Установка модели
	 * @param A1_Model_Entity_Abstract $model
	 * @return ModelStorage
	 */
	public function setModel($model) 
	{
		$this->_model = $model;
		
		#применяем сортировку по умолчанию
		if ($orders = $this->_model->getPart('order')) {
			$this->setOrders($orders);
		}
		return $this;
	}
	
	/**
	 * Получение модели
	 * @return A1_Model_Entity_Abstract
	 */
	public function getModel() 
	{
		return $this->_model;
	}
		
	/**
	 * @return A1_Model_Entity_Abstract
	 */
	public function setWhere($where)
	{
		$this->_where = $where;
		return $this;
	}
	
	/**
     * Get field of data that is identifier
     * @return String
	 */
	public function getIdField() 
	{	
		return !empty($this->_idField) ? $this->_idField : $this->getModel()->getIdField();
	}

    /**
     * @inheritdoc
     */
    public function load($limit = null)
	{
		//A1_Log::getInstance()->timeStart('grid load');
		
		#Генерация события предзагрузки
		$this->fireEvent('before_load', array('storage' => $this));
		
		$model = $this->getModel();
		$model -> selColumns();
    	$this->_where && $model -> selWhere();

    	#сортировка, фильтрация
    	$this->filter()->order();
    	
    	#К-во выбранных записей
    	//A1_Log::getInstance()->timeStart('grid count');
    	
    	$this->_count = $model -> count();
    	
    	//_fb('К-во записей выборки ('.$this->_count.') :'.A1_Log::getInstance()->timeEnd('grid count'));
    	
    	#Генерация события загрузки данных (применены сортировки, фильтры )
		$this->fireEvent('load', array('storage' => $this));
		
		#лимит
    	//_fb('Страница: '.$this->getPage());

    	if ($limit) {
    		$model -> limit($limit);
    	} else {
            $model -> limit($this->getOnPage(), ($this->getPage()-1)*$this->getOnPage());
    	}

    	//_fb('Запрос : '.$model->getSelect().'');
		
		#данные
    	$rows = $model -> findAll();
		$this -> setData($rows);
		
		#Генерация события послезагрузки
		$this->fireEvent('after_load', array('storage' => $this, 'data' => $rows));
		
		//_fb('Загрузка данных : '.A1_Log::getInstance()->timeEnd('grid load'));
		
		return $this;
	}

    /**
     * @inheritdoc
     */
    public function order()
	{
		foreach ($this->_orders as $name=>$dir) {
			$arr = explode('.', $name);
			$clearName = array_pop($arr);
			$method = '_order'.preg_replace("#_([\w])#e", "ucfirst('\\1')", ucfirst($clearName));			
			if (method_exists($this, $method)) {
				call_user_func(array($this, $method), $dir);
			} else {
				$this->getModel()->selOrder(array($name => $dir));
			}
		}
		return $this;
	}

    /**
     * @inheritdoc
     */
	public function filter()
	{
		foreach ($this->_filters as $filter) {
			$method = '_filter'.preg_replace("#_([\w])#e", "ucfirst('\\1')", ucfirst($filter['name']));	
			if (method_exists($this, $method)) {
				call_user_func(array($this, $method), $filter['value']);
			} else {
				$this->getModel()->filter($filter['field'], $filter['operation'], $filter['value'], $filter['function']);
			}	
		}
		return $this;
	}

    /**
     * @inheritdoc
     */
	public function getTotal()
	{
		return $this->getModel()->count();
	}

    /**
     * @inheritdoc
     */
	public function __clone()
	{
		$this->_model = clone $this->_model;
	}
}