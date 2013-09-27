<?php
namespace Widget\Grid\Storage;
use Widget\Helper;
use Widget\ObserverAbstract;

/**
 * Storage of data
 *
 * Class AbstractStorage
 * @package Widget\Grid\Storage
 */
abstract class AbstractStorage extends ObserverAbstract
{	
	/**
	 * Поле идентификатора данных
	 * @var String
	 */
	protected $_idField = '';
	
	/**
	 * Данные 
	 * @var String
	 */
	protected $_data = array();
	
	/**
	 * Список фильтров
	 * @var Array (field, value, operation)
	 */
	protected $_filters = array();
	
	/**
	 * Список сортировок
	 * @var Array
	 */
	protected $_orders = array();
	
	/**
	 * Список сортировок по умолчанию
	 * @var Array
	 */
	protected $_defaultOrders = array();
	
	/**
	 * К-во выбранных записей
	 * @var Integer
	 */
	protected $_count = 0;
	
	/**
	 * К-во на странице
	 * @var Integer
	 */
	protected $_onPage = 100000000;
	
	/**
	 * Текущая страница
	 * @var Integer
	 */
	protected $_page = 1;
	
	/**
	 * @var Array
	 */
	protected $_baseParams = array();
	
	/**
	 * @param Array $options
	 */
	public function __construct($options = array())
	{
		Helper::setConstructorOptions($this, $options);
		
		$this->_init();
	}

    /**
     * @param array|string $storage
     * @return AbstractStorage
     * @throws \Exception
     */
    public static function factory($storage)
    {
        if (is_array($storage)) {
            if (!empty($storage['class']) && class_exists($storage['class'])) {
                $class = $storage['class'];
            } elseif (!empty($storage['class']) && class_exists('\Widget\Grid\Storage\\'.ucfirst($storage['class']).'Storage')) {
                $class = '\Widget\Grid\Storage\\'.ucfirst($storage['class']).'Storage';
            }
            $storage = new $class($storage);
        } elseif (is_string($storage)) {
            $storage = new $storage();
        } elseif (is_object($storage)) {

        } else {
            throw new \Exception('Unknown configuration');
        }

        return $storage;
    }


	/**
	 * Инициализация
	 */
	protected function _init()
	{
		$this->_orders = $this->_orders + $this->_defaultOrders;
	}
	
	/**
	 * Добавить фильтр
	 * addFilter('brand', 'p.brand_id', array(1,2,3), 'IN(?)');
	 * addFilter('name', 'p.name', '%Пиво%', 'LIKE LOWER(?)', 'LOWER');
	 * 
	 * @param String $name
	 * @param String $field
	 * @param String $value
	 * @param String $operation
	 * @param String $function - функция что распостраняеться на $field, LOWER($field)
	 * @return AbstractStorage
	 */
	public function addFilter($name, $field, $value, $operation = ' = ?', $function = null)
	{
		$this->_filters[] = array('name' => $name, 'field' => $field, 'value' => $value, 'operation' => $operation, 'function' => $function);
		return $this;
	}
	
	/**
	 * Добавить сортировку
	 * @exampl addOrder('name', 'asc');
	 * 
	 * @param String $name
	 * @param String $dir
	 * @return AbstractStorage
	 */
	public function addOrder($name, $dir)
	{
		$this->_orders[$name] = $dir;
		return $this;
	}
	
	/**
	 * Установить сортировки
	 * @example setOrders(array('name'=>'asc', 'description' => 'desc));
	 * 
	 * @param Array $orders
	 * @return AbstractStorage
	 */
	public function setOrders($orders)
	{
		$this->_orders = $orders;
		return $this;
	}
	
	/**
	 * Установить сортировки по умолчанию
	 * @example setOrders(array('name'=>'asc','description' => 'desc));
	 * 
	 * @param Array $orders
	 * @return AbstractStorage
	 */
	public function setDefaultOrders($orders)
	{
		$this->_defaultOrders = $orders;
		return $this;
	}
	
	/**
	 * @return Array
	 */
	public function getOrders()
	{
		return $this->_orders;
	}
	
	/**
	 * @return Array
	 */
	public function getDefaultOrders()
	{
		return $this->_defaultOrders;
	}
	
	/**
	 * @return Array
	 */
	public function getFilters()
	{
		return $this->_filters;
	}
	
	/**
	 * Проверка - сортировка по полую
	 * @param String $name
	 * @return Boolean
	 */
	public function isOrder($name)
	{
		return !empty($this->_orders[$name]) ? $this->_orders[$name] : false;
	}
	
	/**
	 * @return Array
	 */
	public function getData() 
	{
		return $this->_data;
	}

	/**
	 * @param String $title
	 * @return AbstractStorage
	 */
	public function setData($data) 
	{
		$this->_data = $data;
		return $this;
	}
	
	/**
	 * @param String $id
	 * @return AbstractStorage
	 */
	public function setIdField($id) 
	{
		$this->_idField = $id;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getIdField() 
	{		
		return $this->_idField;
	}
	
	/**
	 * @param Integer $onpage
	 * @return AbstractStorage
	 */
	public function setOnPage($onpage) 
	{
		$this->_onPage = $onpage;
		return $this;
	}
	
	/**
	 * @return Integer
	 */
	public function getOnPage() 
	{
		return $this->_onPage;
	}
	
	/**
	 * @param Integer $page
	 * @return AbstractStorage
	 */
	public function setPage($page) 
	{
		$this->_page = $page;
		return $this;
	}
	
	/**
	 * @return Integer
	 */
	public function getPage() 
	{
		return $this->_page;
	}

    /**
     * @return integer
     */
    public function getCount()
	{
		return $this->_count;
	}
	
	/**
	 * Сортировка данных
	 * @return AbstractStorage
	 */
	abstract public function order();
	
	/**
	 * Фильтрация данных
	 * @return AbstractStorage
	 */
	abstract public function filter();
	
	/**
	 * Загрузка данных
	 * @param $limit|Null К-во записей выборки
	 * @return AbstractStorage
	 */
	abstract public function load($limit = null);
	
	/**
	 * Общее к-во записей
	 * @return AbstractStorage
	 */
	abstract public function getTotal();
		 
}