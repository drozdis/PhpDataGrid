<?php
namespace Widget\Grid\Storage;

use Widget\ObserverAbstract;

/**
 * Storage of data
 *
 * Class AbstractStorage
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
abstract class AbstractStorage extends ObserverAbstract
{
    /**
     * Поле идентификатора данных
     *
     * @var string
     */
    protected $idField = '';

    /**
     * Данные
     *
     * @var string
     */
    protected $data = array();

    /**
     * Список фильтров
     *
     * @var array (field, value, operation)
     */
    protected $filters = array();

    /**
     * Список сортировок
     *
     * @var array
     */
    protected $orders = array();

    /**
     * Список сортировок по умолчанию
     *
     * @var array
     */
    protected $defaultOrders = array();

    /**
     * К-во выбранных записей
     *
     * @var Integer
     */
    protected $count = 0;

    /**
     * К-во на странице
     *
     * @var Integer
     */
    protected $onPage = 100000000;

    /**
     * Текущая страница
     *
     * @var Integer
     */
    protected $page = 1;

    /**
     * @var array
     */
    protected $baseParams = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = $this->orders + $this->defaultOrders;

        $this->init();
    }

    /**
     * initizlize
     */
    protected function init()
    {

    }

    /**
     * addFilter('brand', 'p.brand_id', array(1,2,3), 'IN(?)');
     * addFilter('name', 'p.name', '%Пиво%', 'LIKE LOWER(?)', 'LOWER');
     *
     * @param string $name
     * @param string $field
     * @param string $value
     * @param string $operation
     * @param string $function функция что распостраняеться на $field, LOWER($field)
     *
     * @return AbstractStorage
     */
    public function addFilter($name, $field, $value, $operation = ' = ?', $function = null)
    {
        $this->filters[] = array('name' => $name, 'field' => $field, 'value' => $value, 'operation' => $operation, 'function' => $function);

        return $this;
    }

    /**
     * <code>
     *   $storage->addOrder('name', 'asc');
     * </code>
     *
     * @param string $name
     * @param string $dir
     *
     * @return AbstractStorage
     */
    public function addOrder($name, $dir)
    {
        $this->orders[$name] = $dir;

        return $this;
    }

    /**
     * Set multiple orders
     * <code>
     *   $storage->setOrders(array('name'=>'asc', 'description' => 'desc));
     * </code>
     *
     * @param array $orders
     *
     * @return AbstractStorage
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * @param array $orders
     *
     * @return AbstractStorage
     */
    public function setDefaultOrders($orders)
    {
        $this->defaultOrders = $orders;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return array
     */
    public function getDefaultOrders()
    {
        return $this->defaultOrders;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $name
     *
     * @return Boolean
     */
    public function isOrder($name)
    {
        return !empty($this->orders[$name]) ? $this->orders[$name] : false;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return AbstractStorage
     */
    public function setIdField($id)
    {
        $this->idField = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * @param Integer $onpage
     *
     * @return AbstractStorage
     */
    public function setOnPage($onpage)
    {
        $this->onPage = $onpage;

        return $this;
    }

    /**
     * @return Integer
     */
    public function getOnPage()
    {
        return $this->onPage;
    }

    /**
     * @param Integer $page
     *
     * @return AbstractStorage
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return Integer
     */
    public function getPage()
    {
        return $this->page ? $this->page : 1;
    }

    /**
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $row
     *
     * @return integer
     */
    public function getId($row)
    {
        return \Widget\Helper::getValue($row, $this->getIdField());
    }

    /**
     * Сортировка данных
     *
     * @return AbstractStorage
     */
    abstract public function order();

    /**
     * Фильтрация данных
     *
     * @return AbstractStorage
     */
    abstract public function filter();

    /**
     * Загрузка данных
     *
     * @param $limit |Null К-во записей выборки
     *
     * @return AbstractStorage
     */
    abstract public function load($limit = null);

    /**
     * Общее к-во записей
     *
     * @return AbstractStorage
     */
    abstract public function getTotal();

}
