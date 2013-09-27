<?php
namespace Widget\Grid\Filter;

use Widget\Grid\Column\Column;
use Widget\Grid\Storage\AbstractStorage;
use Widget\Helper;
use Widget\ObserverAbstract;
use Widget\ObserverListener;
use Widget\RenderInterface;

/**
 * Клас фильтра колонки
 * 
 * @package Widget\Grid\Filter
 * @author drozd
 */
abstract class AbstractFilter extends ObserverAbstract implements RenderInterface
{		
	/**
	 * @var Column
	 */
	protected $_column = null;
	
	/**
	 * Тип фильтра (integer|string) 
	 * @var String
	 */
	protected $_type = 'string';
	
	/**
	 * Колонка БД по которой необходимо фильтровать 
	 * @var String
	 */
	protected $_field = '';
		
	/**
	 * Значение фильтра
	 * @var Mixed
	 */
	protected $_value = '';
	
	/**
	 * Сохранять состояние фильтра или нет
	 * @var Boolean
	 */
	protected $_state = false;
	
	/**
	 * @param Array $options
	 */
	public function __construct($options = array())
	{
		Helper::setConstructorOptions($this, $options);
	}

	/**
	 * @return Boolean
	 */
	public function isState()
	{
		return $this->_state;
	}
	
	/**
	 * @param Boolean $state
	 * @return AbstractFilter
	 */
	public function setState($state)
	{
		$this->_state = $state;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getType() 
	{
		return $this->_type;
	}
	
	/**
	 * @param String $type
	 * @return AbstractFilter
	 */
	public function setType($type) 
	{
		$this->_type = $type;
		return $this;
	}		

	/**
	 * @return String
	 */
	public function getField() 
	{
		return !empty($this->_field) ? $this->_field : $this->getColumn()->getName();
	}
	
	/**
	 * @param String $field
	 * @return AbstractFilter
	 */
	public function setField($field) 
	{
		$this->_field = $field;
		return $this;
	}
	
	/**
	 * @return Mixed
	 */
	public function getValue() 
	{
		return $this->_value;
	}
	
	/**
	 * @param Mixed $value
	 * @return AbstractFilter
	 */
	public function setValue($value) 
	{
		$this->_value = $value;
		return $this;
	}
   	
   	/**
   	 * @param String $column
     * @return AbstractFilter
   	 */
    public function setColumn($column)
    {
    	$this->_column = $column;

        #configure callback
        $callback = function($grid) {
            $grid->getStorage()->on('before_load', new ObserverListener(array('method' => array($this, 'apply'))));
        };
        $column->on('set_grid', new ObserverListener(array('method' => $callback)));

        return $this;
    }

    /**
     * @return \Widget\Grid\Column\Column
     */
 	public function getColumn()
    {
    	return $this->_column;
    }
    
    /**
     * @return \Widget\Grid\Grid
     */
 	public function getGrid()
    {
    	return $this->getColumn()->getGrid();
    }

    /**
     * @return String
     */
    abstract public function render();
    
    /**
     * @param AbstractStorage
     * @return AbstractFilter
     */
    public function apply(AbstractStorage $store)
    {
		$value = $this->getValue();
		if ($value !== null && $value !== '') {
			switch ($this->getType()) {
				case 'integer':
					if (is_array($value)) {
						$value = join(',', $value);
					}
					$value = str_replace(array(', ',' ,'), ',', preg_replace('#\s+#', ' ', $value));
					$value = array_map('intval', explode(',', str_replace(' ', ',', trim($value))));
					$store->addFilter($this->getColumn()->getName(), $this->getField(), $value, 'IN (?)');
				break;
				
				default:
					$arr = explode(' ', trim($value));
					foreach ($arr as &$row) {
						$store->addFilter($this->getColumn()->getName(), $this->getField(), '%'.$row.'%', 'LIKE LOWER(?)', 'LOWER');
					}
				break;
			}
		}
		return $this;
    }
}