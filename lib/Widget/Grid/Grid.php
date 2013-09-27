<?php
namespace Widget\Grid;
use Widget\AbstractWidget;
use Widget\Grid\Action\Action;
use Widget\Grid\Column\Column;
use Widget\Grid\State\State;
use Widget\Grid\Storage\AbstractStorage;
use Widget\Grid\Toolbar\Toolbar;
use Widget\Helper;

/**
 * Class Grid
 * @package Widget
 */
class Grid extends AbstractWidget
{
	/**
	 * Конфигурационные опиции
	 * @var Array
	 */
	protected $_options = array();
	
	/**
	 * Конфигурационные опиции
	 * @var Array
	 */
	protected static $_defaults = array(
        'storage' => array(
            'class'   => 'Widget\Grid\Storage\ArrayStorage',
            'idField' => 'id'
        ),
        'topToolbar' => array(
            'class' => 'Widget\Grid\Toolbar\DefaultToolbar'
        ),
        'bottomToolbar' => false,
        'replaceUrl'    => true,
        'autoLoad'      => false,
        'uriDelimeter'  => '/',
        'extensions'    => array('\Widget\Grid\Extension\Columns', '\Widget\Grid\Extension\Export', '\Widget\Grid\Extension\Pagination'),
        'template'      => '<div class="grid_ct">
                                <div id="{{id}}">
                                    {{topToolbar}}
                                    <table class="table table-striped table-bordered table-condensed">
                                    {{header}}
                                    {{filters}}
                                    {{body}}
                                    </table>
                                    {{bottomToolbar}}
                                    {{footer}}
                                </div>
                            </div>'
	);

	/**
	 * @var \Widget\Grid\Column\Column[]
	 */
	protected $_columns = array(); 
	
	/**
	 * Хеш для быстроко поиска колонок по полю
	 * @var \Widget\Grid\Column\Column[]
	 */
	protected $_columnsByField = array();

	/**
	 * @var \Widget\Grid\Action\Action[]
	 */
	protected $_actions = array(); 
	
	/**
	 * @var \Widget\Grid\Storage\AbstractStorage
	 */
	protected $_storage = null;

	/**
	 * @var String
	 */
	protected $_baseUrl = '';
	
	/**
	 * @var String
	 */
	protected $_uriDelimeter = '/';

	/**
	 * @var Boolean
	 */
	public $selection = false;
	
	/**
	 * @var Toolbar\Toolbar
	 */
	protected $_topToolbar = null;

    /**
     * @var Toolbar\Toolbar
     */
    protected $_bottomToolbar = null;
	
	/**
	 * Параметры урл
	 * @var Array
	 */
	protected $_urlParams = array();
	
	/**
	 * Параметры, которые передаються в грид и участвуют в УРЛ
	 * Для каждой пары ключ/значения вызываеться метод set  
	 * @var Array
	 */
	protected $_params = array();

    //------------------------------------------------------------------------------------------------------------------
	/**
	 * @var \Widget\Grid\State\State
	 */
	protected $_state = null;

    /**
     * Сохранять/не сохранять состояние
     * @var Boolean
     */
    protected $_saveState = true;
    //------------------------------------------------------------------------------------------------------------------
    
	/**
	 * Заменять урл а адресной строке на урл таблици
	 * @var Boolean
	 */
	protected $_replaceUrl = true;
	
	/**
	 * Подгружать данные автоматически после рендеринга таблицы
	 * @var Boolean
	 */
	protected $_autoLoad = false;
	
	/**
	 * Хранение значений фильтров, импользуеться в случае добавления колонок после создания грида
	 * @var Array
	 */
	protected $_filterValues = array();

    /**
     * @var string
     */
    protected $_template = '';

    /**
     * @inheritdoc
     */
    public function __construct($options = array())
	{
        $options = Helper::mergeOptions(self::$_defaults, $options, true);
		$options = Helper::mergeOptions($this->_options, $options);

		$constructor = array();
		$ordering = array('storage', 'columns');

		foreach ($ordering as $order) {
			if (!empty($options[$order])) {
				$constructor[$order] = $options[$order];
				unset($options[$order]);
			}
		}
		$options = array_merge($constructor, $options);
		
		#доп. данные
		$params = $this->getUrlParams('params', true);
		if (!empty($params)) {
			$options['params'] = $params;
		}
				
		#конструктор
		parent::__construct($options);		
		
		#инициализация состояния таблицы
		$this->applyState();
		
		#параметры
		$this->_initGrid();
	}

    /**
     * @param Array $defaults
     */
    public static function setDefaults($defaults)
    {
        self::$_defaults = $defaults;
    }

    /**
     * @return Array
     */
    public static function getDefaults()
    {
        return self::$_defaults;
    }

	/**
	 * Установить колонки
	 * @param \Widget\Grid\Column\Column[] $columns
	 * @return Grid
	 */
	public function setColumns($columns)
	{
		foreach ($columns as $name=>&$column) {
			$this->addColumn($name, $column);
		}
		return $this;
	}
	
	/**
	 * @return \Widget\Grid\Column\Column[]
	 */
	public function getColumns() 
	{
		return $this->_columns;
	}
		
	/**
	 * @param String $name
	 * @return Column
	 */
	public function getColumn($name)
	{
		return !empty($this->_columns[$name]) ? $this->_columns[$name] : false;
	}
	
	/**
	 * Получение колонки по полю field
	 * @param String $name
	 * @return Column
	 */
	public function getColumnByField($field)
	{
		return !empty($this->_columnsByField[$field]) ? $this->_columnsByField[$field] : (!empty($this->_columns[$field]) ? $this->_columns[$field] : false);
	}
	
	/**
	 * @param String $name
	 * @return Grid
	 */
	public function removeColumn($name) 
	{
		unset($this->_columns[$name]);
		return $this;
	}
	
	/**
     * Добавление колонки
     * @param String $name
     * @param Array|Column $column
     * @return Grid
     */
    public function addColumn($name, $column)
    {
    	$column = $this->createColumns($name, $column);
    	$this->_columns[$name] = $column;    
    	$this->_columnsByField[$column->getField()] = $column;
    	return $this;
    }
    
	/**
     * Вставка колонки
     * @param Integer $position
     * @param String $name
     * @param Array|Column $column
     * @return Grid
     */
    public function insert($position, $name, $column)
    {
    	$column = $this->_createColumns($name, $column);
    	
    	if ($position == count($this->_columns)) {
    		$this->addColumn($name, $column);
    	} else {
	    	$index = 1;
	    	$columns = array();
	    	foreach ($this->_columns as $existName=>$exist) {
	    		if ($index == $position) {
	    			$columns[$name] = $column;
	    		}
	    		$columns[$existName] = $exist;
	    		$index++;
	    	}
	    	$this->_columns = $columns;
    	}
    	return $this;
    }

    /**
     * Очистка фильтров
     * @return Grid
     */
    public function clearFilter()
    {
        foreach ($this->getColumns() as $name => $column) {
            if (($filter = $column->getFilter())) {
                $filter->setValue(null);
            }
        }
        return $this;
    }
	
	/**
     * Factory method
	 * Create column
     *
	 * _createColumns('category',
	 * 	 array(
	 *		'title' => 'Категория',
	 *		'dataIndex' => 'category.name',
	 *		'sortable' => true,
	 *		'width' => 200,
	 *		'filter' => array(
	 *			'class' => 'tree',
	 *			'idField' => 'id',
	 *			'titleField' => 'name',
	 *			'type' => 'integer',
	 *			'field' => 'p.parent_id',
	 *		)
	 *	 )
	 * )
	 *
	 * @param String $name
	 * @param Array|Column $column
	 * @return Array
	 */
	public function createColumns($name, $column)
	{
        $column = Column::factory($column, $name);

        #set grid
        $column -> setGrid($this);
		
		#position
		if (!$column->getPosition()) {
			$column -> setPosition(count($this->_columns)+1);
		}
		
		#apply filter value to column
		if (isset($this->_filterValues[$column->getName()]) && $filter = $column -> getFilter()) {
			$filter->setValue($this->_filterValues[$column->getName()]);
		}
		
		return $column;
	}
	
	/**
     * Factory method
	 * Set storage to the grid
	 *
     * @example
     * array(
     *		'class' => 'model', #ModelStorage
     *		'model' => new CategoryModel()
     * )
     *
     * array(
     *		'class' => 'MyApp\Grid\MyStorage',
     *      'param1' => 'value1',
     *      ...
     * )
     *
	 * @param  \Widget\Grid\Storage\AbstractStorage|Array $storage
	 * @return Grid
	 */
	public function setStorage($storage)
	{
		$this->_storage = AbstractStorage::factory($storage);
		return $this;
	}
	
	/**
	 * @return \Widget\Grid\Storage\AbstractStorage
	 */
	public function getStorage()
	{
		return $this->_storage;
	}

	/**
	 * Билдер метод, если задать как массив
	 * @param \Widget\Grid\Toolbar\Toolbar|Array $toolbar
	 * @return Grid
	 */
	public function setTopToolbar($toolbar)
	{
		$this->_topToolbar = Toolbar::factory($toolbar, $this);
		return $this;
	}
	
	/**
	 * @return \Widget\Grid\Toolbar\Toolbar
	 */
	public function getTopToolbar() 
	{		
		return $this->_topToolbar;
	}

    /**
     * Билдер метод, если задать как массив
     * @param \Widget\Grid\Toolbar\Toolbar|Array $toolbar
     * @return Grid
     */
    public function setBottomToolbar($toolbar)
    {
        $this->_bottomToolbar = Toolbar::factory($toolbar, $this);
        return $this;
    }

    /**
     * @return \Widget\Grid\Toolbar\Toolbar
     */
    public function getBottomToolbar()
    {
        return $this->_bottomToolbar;
    }

	/**
	 * Базовый урл 
	 * @param String $url
	 * @return Grid
	 */
	public function setBaseUrl($url)
	{
		$this->_baseUrl = $url;
		return $this;
	}
	
	/**
	 * @return String
	 */
	public function getBaseUrl() 
	{
		return $this->_baseUrl;
	}
	
	/**
	 * Базовый урл
	 * @param Boolean $replaceUrl
	 * @return Grid
	 */
	public function setReplaceUrl($replaceUrl)
	{
		$this->_replaceUrl = $replaceUrl;
		return $this;
	}
	
	/**
	 * @return Boolean
	 */
	public function getReplaceUrl()
	{
		return $this->_replaceUrl;
	}

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

	/**
	 * @param String $auto
	 * @return Grid
	 */
	public function setAutoLoad($auto)
	{
		$this->_autoLoad = $auto;
		return $this;
	}
	
	/**
	 * @return Boolean
	 */
	public function getAutoLoad()
	{
		return $this->_autoLoad;
	}

    /**
     * @param String $uriDelimeter
     * @return Grid
     */
    public function setUriDelimeter($uriDelimeter)
    {
        $this->_uriDelimeter = $uriDelimeter;
        return $this;
    }

    /**
     * @return String
     */
    public function getUriDelimeter()
    {
        return $this->_uriDelimeter;
    }

	/**
	 * @return Boolean
	 */
	public function getSelection()
	{
		return $this->selection;
	}
	
	/**
	 * @param boolean $selection
	 * @return Grid
	 */
	public function setSelection($selection)
	{
		$this->selection = $selection;
		return $this;
	}
	
	/**
	 * Урл с учетем сортировок, фильтров
	 * @return String
	 */
	public function getUrl($mixer = array()) 
	{
		return $this->_url($mixer);
	}

	/**
	 * Установить параметры УРЛ ($_GET, $_POST - парметры)
	 * @param String $url
	 * @return Grid
	 */
	protected function _initGrid() 
	{
		#фильтрация
		$filter = $this->getUrlParams('filter', true);
		if (!empty($filter)) {
			$this->applyFilter($filter);	
		}
		
		#сортировка
		$order = $this->getUrlParams('order', true);
		if (!empty($order)) {
			$this->applyOrder($order);
		}

        // отключаем drag'n'drop если есть сортировка по колонке
        foreach($this->getColumns() as $column) {
            if ($column instanceof A1_Widget_Grid_Column_Sorting) {
                $column->setHidden(!empty($order));
            }
        }
		
		#страница
//		$page = $this->getUrlParams('page');
//		if ($this->getTopToolbar() && $this->getTopToolbar()->getPaginator()) {
//			$this->getTopToolbar()->getPaginator()->setPage($page);
//			$this->getStorage()->setPage($page);
//		}
		
//		$onpage = $this->getUrlParams('onpage');
//		if ($this->getTopToolbar() && $this->getTopToolbar()->getPaginator() && $onpage) {
//			$this->getTopToolbar()->getPaginator()->setOnPage($onpage);
//		}
		
		#вкл/выкл колонки
		$extensionColumns   = $this->getUrlParams('extension-columns', false);
		if (!empty($extensionColumns['columns'])) {
			foreach ($extensionColumns['columns'] as $i=>$name) {
				$name = str_replace('col-', '', $name);
				if ($column = $this->getColumn($name)) {
					$column->setHidden(false)->setPosition($i+1);
				}
			}
		}
		if (!empty($extensionColumns['disabled'])) {
			foreach ($extensionColumns['disabled'] as $j=>$name) {
				$name = str_replace('col-', '', $name);
				if ($column = $this->getColumn($name)) {
					$column->setHidden(true)->setPosition($j+count($extensionColumns['columns'])+1);
				}
			}
		}
		if (!empty($extensionColumns['clear'])) {
			$i = 1;
			foreach ($this->getColumns()  as $column) {
				$column->setPosition($i++)->setHidden($column->getOptions('hidden'));
			}
		}
		
		return $this;
	}
	
	/**
	 * Получить параметры УРЛ
	 * @param String|Null 
	 * @return String
	 */
	public function getUrlParams($name = null, $encode = false) 
	{
		$params = $_REQUEST;// Zend_Controller_Front::getInstance()->getRequest()->getParams();
		if ($name === null) {
			return $params;
		}
		$value = !empty($params[$name]) ? $params[$name] : '';
		return $encode === true ? Helper::getParam($value) : $value;
	} 
	
	/**
	 * @param Array $params
	 * @return Grid
	 */
	public function setParams($params)
	{
		$this->_params = $params;
		foreach ((array)$this->_params as $key=>$vlaue) {
			$method = 'set'.preg_replace("#_([\w])#e", "ucfirst('\\1')", ucfirst($key));
			if (method_exists($this, $method)) {
				call_user_func(array($this, $method), $vlaue);
			}
		}
		return $this;
	}
	
	/**
	 * Получить параметры
	 * @return Array
	 */
	public function getParams()
	{
		return $this->_params;
	}
	
	/**
	 * Применение фильтра к таблице
	 * @param Array $filter
	 * @return Grid
	 */
	public function applyFilter($filter) 
	{
		$filter = Helper::arrayMap('urldecode', $filter);
		$data   = Helper::filterNotEmpty($filter);
		
		$this->_filterValues = $data;
		
		foreach ($data as $name => $value) {
			if (($column = $this->getColumn($name)) && ($filter = $column -> getFilter()) && $column -> getHidden() == false) {
				$filter->setValue($value);
			}
		}
		
		return $this;
	}
	
	/**
	 * Применение сортировки к таблице
	 * @param Array $order
	 * @return Grid
	 */
	public function applyOrder($order) 
	{
		$this->getStorage()->setOrders(array());
		foreach ($order as $name=>&$dir) {
			$column = $this->getColumnByField($name);
			if (!empty($column) && $column -> getHidden() == false && $column -> isSortable()) {
				$this->getStorage()->addOrder($column -> getField(), $dir);
			}
		}
		return $this;
	}


    /**
     * array('remove' => array(
     *		'title' => 'Удалить',
     *		'href'   => '/aaa/remove',
     * ))
     *
     * array('edit' => 'Edit', 'remove' => 'Remove item')
     *
     * @param array|\Widget\Grid\Action\Action[] $actions
     * @return Toolbar
     */
    public function setActions($actions)
    {
        $this->_actions = array();
        foreach ($actions as $name => &$action) {
            $this->addAction($name, $action);
        }

        return $this;
    }

    /**
     * @return \Widget\Grid\Action\Action[]
     */
    public function getActions()
    {
        return $this->_actions;
    }

    /**
     * addAction('remove', array(
     *      'title'  => 'Удалить',
     *		'href'   => '/aaa/remove',
     *		'icon'   => 'remove',
     * ));
     *
     * @param string $name
     * @param array|\Widget\Grid\Action\Action $action
     * @return Grid
     */
    public function addAction($name, $action)
    {
        $action = Action::factory($action, $name);
        $action -> setGrid($this);

        $this->_actions[$name] = $action;
        return $this;
    }

	/**
	 * @return String
	 */
	public function getJavascriptObject() 
	{
		return $this->_name.'Widget';			
	}
	
	/**
	 * Reorder columns by position ($column->getPosition())
	 * @return Grid
	 */
	public function reorderColumns()
	{
		#сортировка колонок
		$positions = false;
		foreach ($this->getColumns() as $column) {
			$positions = $positions || $column->getPosition() > 0;
		}
		if ($positions) {
			if (!function_exists('sort_columns')) {
				function sort_columns($a, $b)
				{
					if ($a->getPosition() == $b->getPosition()) {
						return -1;
					}
					return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
				}
			}
			uasort($this->_columns, 'Widget\Grid\sort_columns');
		}
		return $this;
	} 
	
	/**
	 * (non-PHPdoc)
	 * @see AbstractWidget::render()
	 */
	public function _initialHtml()
	{
        $this->getResourceManager()->addJavascriptFile(array(
            '/shared/js/jquery/extensions/jquery.json-2.3.min.js',
            '/shared/js/a1/grid.js',
            '/shared/js/a1/json.js',
            '/shared/js/a1/url.js'
        ));

		#перестановка колонок согласно позициям
		$this->reorderColumns();

        $rendered = array(
            'id'     => $this->_name,
            'header' =>  $this->_renderHeader(),
            'filters'=>  $this->_renderFilters(),
            'body'   =>  $this->_renderBody(),
            'footer' =>  $this->_renderFooter(),
        );

		if ($topToolbar = $this->getTopToolbar()) {
            $rendered['topToolbar'] = $topToolbar->render();
        }
        if ($bToolbar = $this->getBottomToolbar()) {
            $rendered['bottomToolbar'] = $bToolbar->render();
        }

        $html = $this->getTemplate();
        foreach ($rendered as $key => $value) {
            $html = str_replace('{{'.$key.'}}', $value, $html);
        }

		#автозагрузка
		$json = json_encode(array(
			'id'  => $this->_name,
			'url' => $this->getUrl(array('page' => true)),
			'baseUrl' => $this->getBaseUrl(),
			'replaceUrl' => $this->getReplaceUrl(),
			'autoLoad' => $this->getAutoLoad(),
			'uriDelimeter' => $this->_uriDelimeter
		));
        $js =
            'if (typeof(Widget) != "undefined" && typeof(Widget.Grid) != "undefined" && typeof('.$this->getJavascriptObject().') == "undefined") {'.
                'var '.$this->getJavascriptObject().' = new Widget.Grid('.$json.');'.
            '}';
        $this->getResourceManager()->addJavascript($js);

		return $html;	
	}

    /**
     * @inheritdoc
     */
    public function render()
	{
		$html = parent::render();
		
		#save state
		$this -> saveState();

		return $html;
	}
	
	/**
	 * Отображение шапки талицы
	 * @return Grid
	 */	
	protected function _renderHeader()
	{
		$html = '<colgroup>';
		if ($this->selection !== false) {
			$html .= '<col width="20" class="a-center" />';
		}
		foreach ($this->_columns as &$column) {
			$class = '';
			if ($column->getHidden()) {
				continue;
			}
			$html .= '<col'.($column->getWidth() ? ' width="'.$column->getWidth().'"':'').' '.($class ? ' class="'.$class.'"':'').'/>';
		}
		
		#колонка действий
		if (!empty($this->_actions)) {
			$html .= '<col width="'.(30*count($this->_actions)+6).'" class="a-center" />';
		}
		$html .= '</colgroup>';
		
		$html .= '<tr class="headings">';
		if ($this->selection !== false) {
			$html .= '<th><span class="nobr"><input type="checkbox" data-role="check-all" value="'.$this->getStorage()->getCount().'" onclick="'.$this->getJavascriptObject().'.checkAll(\'selected[]\', this);"/></span></th>';
		}
					
		foreach ($this->_columns as $name=>&$column) {
			$class = '';
			if ($column->getHidden()) {
				continue;
			}
			$html .= '<th data-name="'.$column->getName().'" '.($class ? ' class="'.$class.'"':'').'>';
			
			if ($column->isSortable()) {
				$class = 'no-sort';
				if (($dir = $this->getStorage()->isOrder($column->getField())) !== false) {
					$class = 'sort-'.$dir;
				}
				$html .= '<div class="sort-block '.$class.'">
							<div class="s-sort-wrap">
								<a rel="nofollow" data-role="tooltip" title="'.$column->getHint().'" class="s-sort" href="'.$this->_url(array('order' => array($name => 'add'))).'" onclick="'.$this->getJavascriptObject().'.load(this.href); return false;">'.$column->getTitle().'<span></span></a>
							</div>	
							'.($class !== 'not-sort' ? '<a rel="nofollow" href="'.$this->_url(array('order' => array($name => 'remove'))).'" class="s-close" onclick="'.$this->getJavascriptObject().'.load(this.href); return false;"></a>' : '').'
						  </div>';		
			} else {
				$html .= '<span data-role="tooltip" title="'.$column->getHint().'">'.$column->getTitle().'</span>';
			}
			$html .= '</th>';
		}
		
		#колонка действий
		if (!empty($this->_actions)) {
			$html .= '<th><span class="nobr"></span></th>';
		}
		
		$html .= '</tr>';
		
		return $html;
	}

    /**
     * @return string
     */
    protected function _renderFilters()
    {
        $flag = false;
        $columns = $this->getColumns();
        foreach ($columns as $name=>&$column) {
            if (!$column->getHidden() && $column->getFilter() && $column->isFilterable()) {
                $flag = true;
            }
        }
        if (empty($flag)) {
            return '';
        }

        $html = '';
        $html .= '<tr class="filter">';
        if ($this->getSelection() !== false) {
            $html .= '<td></td>';
        }

        foreach ($columns as &$column) {
            if ($column->getHidden()) {
                continue;
            }
            if ($column->getFilter() && $column->isFilterable()) {
                $html .= '<td><div class="filter-container">'.$column->getFilter()->render().'</div></td>';
            } else {
                $html .= '<td></td>';
            }
        }
        if ($this->getActions()) {
            $html .= '<td></td>';
        }
        $html .= '</tr>';

        return $html;
    }

	/**
	 * Рендеринг данных
	 * @return string
	 */
	protected function _renderBody()
	{
		$html = '';
		$data = $this->getStorage()->getData();
		if (!empty($data)) {
			foreach ($data as $i=>$row) {
				$html .= $this->_renderTr($row,$i);
			}
			$html .= $this->_renderSummary();
		} else {
			$html .= '<tr><td colspan="'.(count($this->_columns)+2).'" style="padding: 10px; text-align: center;">Нет данных</td></tr>';
		}
		return $html;
	}
	
	/**
	 * Rendering footer of the table
	 * @return string
	 */
	protected function _renderFooter()
	{
		return '';
	}
	
	/**
	 * Rendering summary of the table
	 * @return string
	 */
	protected function _renderSummary()
	{
		return '';
	}

	/**
	 * Отображение строк
	 * @param Array $row
	 * @param Integer $index
	 */
	protected function _renderTr($row, $index = 0)
	{
		$idField = $this->getStorage()->getIdField();
		$html = $index % 2 == 0 ? '<tr data-identifier="'.Helper::getValue($row, $idField).'" class="even">' : '<tr data-identifier="'.Helper::getValue($row, $idField).'">';
		if ($this->getSelection()) {
			$html .= '<td class="a-center"><input type="checkbox" name="selected[]" value="'.Helper::getValue($row, $idField).'" /></td>';
		}
		
		#рендеринг
		foreach ($this->_columns as &$column) {
			$html .= $column -> render($row);
		}
		
		#колонка действий
		if (!empty($this->_actions)) {
			$actions = '<div class="btn-group">';
			foreach ($this->_actions as $action) {
                $actions .= $action->setCurrentRow($row)->render();
			}
			$actions .= '</div>';
			$html .= '<td class="last">'.$actions.'</td>';
		}
		
		$html .= '</tr>';
		return $html;
	} 
		
	/**
	 * Построение урл 
	 * @param Array $mixer
	 */
	protected function _url($mixer = array())
	{
		$url = array();
		
		#сортировки
		$storeOrders = $this->getStorage()->getOrders();
		$orders = array();
		foreach ($storeOrders as $name => $dir) {
			$column = $this->getColumnByField($name);
            if (!empty($column) && $column -> getHidden() == false && $column -> isSortable()) {
				$orders[$column->getName()] = $dir;
			}
		}
        
        
		if (!empty($mixer['order'])) {
			$column = $this->getColumnByField(key($mixer['order']));
			$action = current($mixer['order']);
			$name   = $column->getName();
			if ($column) {
    			if ($action == 'remove') {
    				unset($orders[$name]);
    			} elseif (empty($orders[$name])) {
    				$orders[$name] = 'asc';
    			} else {
    				$orders[$name] = $orders[$name]  == 'asc' ? 'desc' : 'asc';
    			}
			}
		}
		!empty($orders) &&  $url[] = $this->_urlValue('order', A1_Helper_Request::url($orders));
		
		
		#фильтрация
		if (!isset($mixer['filter'])) {
			$filterData = array();
			foreach ($this->getColumns() as $name => $column) {
				if (($filter = $column->getFilter())) {
					$value = $filter->getValue();
					if ($value !== '' && $value !== null) {
						$filterData[$name] = $value;
					}
				}
			}
			!empty($filterData) && $url[] = $this->_urlValue('filter', A1_Helper_Request::url($filterData));
		}
		
		#параметры которые переданы гриду в конструкторе
		if ($this->getParams()) {
			$url[] = $this->_urlValue('params', A1_Helper_Request::url($this->getParams()));
		}
		
		#к-во на странице
//		if ($this->getTopToolbar() && $this->getTopToolbar()->getPaginator()) {
//			$onpage = $this->getTopToolbar()->getPaginator()->getOnPage();
//			if (!empty($mixer['onpage'])) {
//				$onpage = $mixer['onpage'];
//			}
//			$onpage && $url[] = $this->_urlValue('onpage', $onpage);
//		}
		
		#страница
		$page = 1;
//		if (!empty($mixer['page']) && $this->getTopToolbar() && $this->getTopToolbar()->getPaginator()) {
//			$page = $this->getTopToolbar()->getPaginator()->getPage();
//		}
		$page > 1 && $url[] = $this->_urlValue('page', $page);
		
		$separator = $this->_uriDelimeter == '/' ? '/' : '?';
		if (strpos($this->getBaseUrl(), '?') !== false) {
			$separator = '&';
		}
		$url = rtrim($this->getBaseUrl(),'/').$separator.join($this->_uriDelimeter, $url);
		return $url;
	}	
	
	
	protected function _urlValue($param, $value)
	{
		#страница
		$uriDelimeter = $this->_uriDelimeter;
		if ($uriDelimeter == '/') {
			$valueDelimeter = '/';
		} else {
			$valueDelimeter = '=';
		}
		return $param.$valueDelimeter.$value;
	}

    /**
     * @param boolean $saveState
     * @return Grid
     */
    public function setSaveState($saveState)
    {
        $this->_saveState = $saveState;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getSaveState()
    {
        return $this->_saveState;
    }

    /**
     * @return \Widget\Grid\State\State
     */
    public function getState()
    {
        if ($this->_state === null) {
            $this->_state = new State(array('grid' => $this));
        }
        return $this->_state;
    }

	/**
	 * Применение состояния
	 * @return Grid
	 */
	public function applyState()
	{
		$params = $this->getUrlParams();
		$state = $this->getState()->getState();
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getSaveState()) {
			if (empty($params['order']) && empty($params['filter'])) {
				if (!empty($state['filters']) && $filters = $this->getFilters()) {
					$this->applyFilter($state['filters']);
				}
				if (!empty($state['orders']) && $storage = $this->getStorage()) {
					$this->applyOrder($state['orders']);
				}
				if (!empty($state['page']) && $storage = $this->getStorage()) {
					$storage->setPage((int)$state['page']);
				}
				if (!empty($state['onpage']) && ($topToolbar = $this->getTopToolbar())) {
					//$topToolbar->getPaginator() && $topToolbar->getPaginator()->setOnPage((int)$state['onpage']);
				}
			}
		}
		
		if (!empty($state['columns'])) {
			foreach ($state['columns'] as $name => $column) {
				foreach ($column as $key => $value) {
					$method = 'set'.preg_replace("#_([\w])#e", "ucfirst('\\1')", ucfirst($key));
					method_exists($this->getColumn($name), $method) && call_user_func(array($this->getColumn($name), $method), $value);
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * Сохранение состояния
	 * @return Grid
	 */
	public function saveState()
	{
		if ($this->getSaveState()) {
			$state = array();
			$keys = array('hidden', 'position');
			$state['columns'] = $state['filters'] = array();
			foreach ($this->getColumns() as $name => $column) {
				#сохранение данных
				foreach ($keys as $key) {
					$method = 'get'.preg_replace("#_([\w])#e", "ucfirst('\\1')", ucfirst($key));
					$state['columns'][$name][$key] = call_user_func(array($column, $method));
				}

                if (($filter = $column->getFilter()) && $filter->isState()) {
                    $state['filters'][$name] = $filter->getValue();
                }
			}
			
			if (!empty($order)) {
				$this->getStorage()->setOrders(array());
				foreach ($order as $name=>&$dir) {
					$column = $this->getColumnByField($name);
					if (!empty($column) && $column -> getHidden() == false && $column -> isSortable()) {
						$this->getStorage()->addOrder($column -> getField(), $dir);
					}
				}
			}
			
			if ($storage = $this->getStorage()) {
				$orders = array();
				$storeOrders = $storage -> getOrders();
				foreach ($this->getColumns() as $column) {
					if ($column -> getHidden() == false && $column -> isSortable() && isset($storeOrders[$column->getField()])) {
						$orders[$column->getField()] = $storeOrders[$column->getField()];
					}
				}
				$state['orders'] = $orders;
				
				$state['page']   = $storage -> getPage();
				$state['onpage'] = $storage -> getOnPage();
			}
			$this->getState()->setState($state);
		}
		return $this;	
	}
}