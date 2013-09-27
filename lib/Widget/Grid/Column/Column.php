<?php
namespace Widget\Grid\Column;
use Widget\AbstractWidget;
use Widget\Grid\Grid;
use Widget\Helper;
use Widget\ObserverAbstract;
use Widget\RenderInterface;

/**
 * Grid column
 *
 * Class Column
 * @package Widget\Grid\Column
 */
class Column extends ObserverAbstract implements RenderInterface
{
    /**
     * @var string
     */
    protected $_name = '';

	/**
	 * Заголовок 
	 * @var String
	 */
	protected $_title = '';

	/**
	 * Подсказка 
	 * @var String
	 */
	protected $_hint = '';
	
	/**
	 * Ширина 
	 * @var String
	 */
	protected $_width = '';
	
	/**
	 * Позиционирования (left, center, right) 
	 * @var String
	 */
	protected $_align = '';
	
	/**
	 * @var boolean
	 */
	protected $_nowrap = false; 
	
	/**
	 * Атрибуты, которые применяются к td (array('class' => 'sample', 'data-index' => 1, style => 'display:block'))
	 * @var Array
	 */
	protected $_attrs = array();
	
	/**
	 * Скрыть/Паказать колонку
	 * @var Boolean
	 */
	protected $_hidden = false;
	
	/**
	 * Вкл/Выкл сортировки
	 * @var String
	 */
	protected $_sortable = false;
	
	/**
	 * Позиция
	 * @var String
	 */
	protected $_position = null;
	
	/**
	 * Ссылка
	 * @var String|Array
	 */
	protected $_url = '';
	
	/**
	 * Путь к данным 
	 * Берем данные по полю не с $row[$name], а с масива, например $row['category['name при dataIndex = category.name
	 * @var String
	 */
	protected $_dataIndex = null;
			
	/**
	 * Поле в БД name -> sp.name
	 * @var String
	 */
	protected $_field = null;
	
	/**
	 * @var AbstractFilter
	 */
	protected $_filter = null;
	
	/**
	 * Включен/Выключен фильтр
	 * @var Boolean
	 */
	protected $_filterable = true;
	
	/**
	 * @var Grid
	 */
	protected $_grid = null;
		
	/**
	 * Начальные настройки колонки
	 * @var Array
	 */
	protected $_options = array(); 
	
	/**
	 * Список автоматический аттачей
	 * @var Array
	 */
	protected $_attachments = array();
	
	/**
	 * @param Array $options
	 */
	public function __construct($options = array())
	{
		$this->_options = $options;
		Helper::setConstructorOptions($this, $options);
	}

    /**
     * @param array|object $column
     * @param string $name
     * @return Column
     */
    public static function factory($column, $name)
    {
        if (is_array($column)) {
            $column['name'] = $name;
            if (!empty($column['class']) && class_exists($column['class'])) {
                $class = $column['class'];
            } elseif (!empty($column['class']) && class_exists('Widget\Grid\Column\\'.ucfirst($column['class']))) {
                $class = 'Widget\Grid\Column\\'.ucfirst($column['class']);
            } else {
                $class = __CLASS__;
            }
            $column = new $class($column);
        } elseif (is_object($column)) {

        }

        return $column;
    }

	/**
	 * @param string $key
	 * @return Mixed
	 */
	public function getOptions($key = null)
	{
		if ($key != null) {
			return !empty($this->_options[$key]) ? $this->_options[$key] : null;
		}
		return $this->_options;
	}

    /**
     * @param string $name
     * @return Column
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
	 * @return String
	 */
	public function getTitle() 
	{
		return $this->_title;
	}
	
	/**
	 * @return String
	 */
	public function getHint() 
	{
		return $this->_hint;
	}	

	/**
	 * @return Integer
	 */
	public function getWidth() 
	{
		return $this->_width;
	}

	/**
	 * @return String
	 */
	public function getAlign() 
	{
		return $this->_align;
	}
	
	/**
	 * @param Integer $position
	 * @return Column
	 */
	public function setPosition($position)
	{
		$this->_position = $position;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getPosition()
	{
		return $this->_position;
	}
	
	/**
	 * Установка переноса строк
	 * 
	 * @param Boolean $wrap
	 * @return Column
	 */
	public function setNowrap($wrap)
	{
		$this->_nowrap = $wrap;
		return $this;
	}
	
	/**
	 * @return Boolean
	 */
	public function isNowrap()
	{
		return $this->_nowrap;
	}

	/**
	 * @return Boolean
	 */
	public function isSortable() 
	{
		return $this->_sortable;
	}
	
	/**
	 * @return Boolean
	 */
	public function isFilterable()
	{
		return $this->_filterable;
	}

	/**
	 * @return String
	 */
	public function getDataIndex() 
	{
		return $this->_dataIndex;
	}

	/**
	 * @param String $title
	 * @return Column
	 */
	public function setTitle($title) 
	{
		$this->_title = $title;
		return $this;
	}
	
	/**
	 * @param String $hint
	 * @return Column
	 */
	public function setHint($hint) 
	{
		$this->_hint = $hint;
		return $this;
	}

	/**
	 * @param String $width
	 * @return Column
	 */
	public function setWidth($width) 
	{
		$this->_width = $width;
		return $this;
	}

	/**
	 * @param String $align
	 * @return Column
	 */
	public function setAlign($align) 
	{
		$this->_align = $align;
		return $this;
	}

	/**
	 * @param String $sortable
	 * @return Column
	 */
	public function setSortable($sortable) 
	{
		$this->_sortable = $sortable;
		return $this;
	}
	
	/**
	 * @param String $filterable
	 * @return Column
	 */
	public function setFilterable($filterable)
	{
		$this->_filterable = $filterable;
		return $this;
	}

	/**
	 * @param String $dataIndex
	 * @return Column
	 */
	public function setDataIndex($dataIndex) 
	{
		$this->_dataIndex = $dataIndex;
		return $this;
	}
	
	/**
	 * @example 
	 *     array(
     *       'core/site' => array('key' => 'site_id', 'to' => 'site'),
	 *     )
	 * @param Array $attachments
	 * @return Column
	 */
	public function setAttachments($attachments)
	{
	    $this->_attachments = $attachments;
	    return $this;
	}
	
	/**
	 * @return Array $attachments
	 */
	public function getAttachments()
	{
	    return $this->_attachments;
	}
	
	/**
	 * Обработка аттачей
	 * @return Column
	 */
	public function processAttachments() 
	{
	    foreach ($this->_attachments as $model => $attach) {
	        $this->getGrid()->getStorage()->getModel()->addAttach($model, $attach);
	    }
	    return $this;
	}
	
	/**
	 * @param Boolean $hidden
	 * @return Column
	 */
	public function setHidden($hidden) 
	{
		$this->_hidden = $hidden;
		return $this;
	}
	
	/**
	 * @return Boolean
	 */
	public function getHidden() 
	{
		return $this->_hidden;
	}

	/**
	 * @return Array $attrs
	 */
	public function getAttrs() 
	{
		return $this->_attrs;
	}
	
	/**
	 * @param Array $attrs
	 * @return Column
	 */
	public function setAttrs($attrs) 
	{
		$this->_attrs = $attrs;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getField()
	{
		return $this->_field ? $this->_field : $this->_name;
	}
	
	/**
	 * @param String $field
	 * @return Column
	 */
	public function setField($field)
	{
		$this->_field = $field;
		return $this;
	}
	
	/**
	 * @param Array|_AbstractFilter|False $filter
	 * @return Column
	 */
	public function setFilter($filter)
	{
		if (!$filter ) {
			$this->_filter = null;
			return $this;
		}
		
		if (is_array($filter)) {
			$class = !empty($filter['class']) ? $filter['class'] : 'text';
			if (class_exists($class)) {
				$filter = new $class($filter);
			} elseif (class_exists('Widget\Grid\Filter\\'.ucfirst($class).'Filter')) {
				$class = 'Widget\Grid\Filter\\'.ucfirst($class).'Filter';
				$filter = new $class($filter);
			} else {
				throw new Exception('Неизвесный класс фильтра '.$class);
			}
		}
		#назначаем колонку
		$filter -> setColumn($this);
		
		#сохраняем
		$this->_filter = $filter;
		
		return $this;
	}
	
	/**
	 * @return AbstractFilter
	 */
	public function getFilter()
	{
		return $this->_filter;
	}
	
	/**
	 * @param Grid $grid
	 * @return Column
	 */
	public function setGrid(Grid $grid)
	{
		$this->_grid = $grid;

        #fire event set grid for column
        $this->fireEvent('set_grid', array('grid' => $grid));

        //@todo attachments
//		#слушаем событие before_load для автоматической обработки аттачей
//		if ($store = $this->getGrid()->getStorage()) {
//            $listener = new A1_Widget_Observer_Listener(array('object' => $this, 'method' => 'processAttachments'));
//	        $store->on('before_load', $listener);
//		}

		return $this;
	}
	
	/**
	 * @return Grid
	 */
	public function getGrid() 
	{
		return $this->_grid;
	}
	
	/**
	 * @example setUrl('/shop/product/{{id}}')
	 * 			setUrl(array(
	 * 				'href' => '/shop/product/{{id}}',
	 * 				'target => 'blank'
 	 * 			)) 
	 * 
	 * @param String $url
	 * @return Column
	 */
	public function setUrl($url) 
	{
		$this->_url = $url;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getUrl() 
	{
		return $this->_url;
	}
	
	/** 
	 * (non-PHPdoc)
	 * @see AbstractWidget::render()
	 */
	public function render($row = array()) 
	{
		$html = '';
		if ($this->getHidden() == false) {
			$attrs = (array)$this->getAttrs();
			 
			$class = !empty($attrs['class']) ? $attrs['class'] : '';
			if ($align = $this->getAlign()) {
				$class .= ' a-'.$align;
			} 
		
			if ($this->isNowrap()) {
				$class .= ' nowrap';
			}
			
			$arr = array();
			foreach ($attrs as $key=>$value) {
				$arr[] = $key.'="'.$value.'"';
			}
			
			$value = $this->_value($row);
			$value === '' && $value = '&nbsp;'; 
			$html = '<td '.($class ? 'class="'.trim($class).'"':'').' '.join(' ', $arr).'>'.$value.'</td>';
		}
		return $html;
	}
	
	/**
	 * Получение значения для поля
	 * @param Array $row
	 * @return Mixed
	 */
	protected function _value($row)
	{
		#получение значения
		$value = $this->_getValueFromRow($row, $this->_dataIndex);
		#если колонка выступает ссылкой
		if ($url = $this->getUrl()) {
			$href = is_array($url) ? $url['href'] : $url;
			$target = is_array($url) && !empty($url['target']) ? $url['target'] : '';
			
			$href = $href.(strpos($href, '?') === false ? '?' : '&').'return='.urlencode($this->getGrid()->getUrl());
			#замена конструкций {{param}} на значение
			if (preg_match_all('#{{([\d\w_]+)}}#', $href, $m)) {
				foreach ($m[1] as $key) {
					$href = str_replace('{{'.$key.'}}', isset($row[$key]) ? $row[$key] : '', $href);
				}
			}
			$value = '<a '.($target ? 'target="'.$target.'"' : '').' href="'.$href.'">'.$value.'</a>';
		}
		
		return $value;
	}
	
	/**
	 * Получение значения
	 * @param Array $row
	 * @return String
	 */
	protected function _getValueFromRow($row, $key)
	{
		if (!empty($key)) {
			$dataIndex = explode('.', $key);
			$value = $row;
			foreach ($dataIndex as $index) {
				if (empty($value) || $value == null) {
					break;
				}
				
				if (isset($value[$index])) {
					$value = $value[$index];
				} else {
					if (array_key_exists($index, $value) && is_null($value[$index])) {
						$value = '';
					} else {
						$value = join('<br/>',A1_Helper_Array::findKey($index, $value));
					}
				}
			}
			if (is_array($value)) {
				$value = join('<br/>', $value);
			}
		} else {
			$value = Helper::getValue($row, $this->_name);
		}
		
		if (is_array($value) && empty($value)) {
			$value = '';
		}
		
		return $value;
	}
	
	/**
	 * Получение значения для колонки
	 * @param Array $row строка
	 * @return Array
	 */
	public function getValue($row)
	{
		return $this->_value($row);
	}
}