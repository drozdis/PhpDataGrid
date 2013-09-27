<?php
namespace Widget\Grid\Toolbar;
use Widget\AbstractWidget;
use Widget\Grid\Grid;
use Widget\Helper;
use Widget\RenderInterface;

/**
 * Клас "Тулбар"
 * 
 * @package A1_Widget
 * @author drozd
 */
class Toolbar implements RenderInterface
{
	/**
	 * @var \Widget\Grid\Grid
	 */
	protected $_grid = null;

    /**
     * @var RenderInterface[]
     */
    protected $_buttons = array();

    /**
     * @var \Widget\RenderInterface[]
     */
    protected $_elements = array();

	/**
	 * @var Action[]
	 */
	protected $_actions = array();
	
	/** 
	 * (non-PHPdoc)
	 * @see AbstractWidget::__construct()
	 */
	public function __construct($options = array()) 
	{
		if (empty($options['grid'])) {
			throw new Zend_Exception('Обязательно необходимо задать таблицу');
		}
		Helper::setConstructorOptions($this, $options);
	}

    /**
     * @param array|boolean|Toolbar $toolbar
     * @param \Widget\Grid\Grid $grid
     * @return null|Toolbar
     * @throws \Exception
     */
    public static function factory($toolbar, $grid)
    {
        if (!$toolbar) {
            return null;
        }

        if (is_array($toolbar)) {
            if (!empty($toolbar['class']) && class_exists($toolbar['class'])) {
                $class = $toolbar['class'];
            } elseif (!empty($toolbar['class']) && class_exists('\Widget\Grid\Toolbar\\'.ucfirst($toolbar['class']))) {
                $class = '\Widget\Grid\Toolbar\\'.ucfirst($toolbar['class']);
            } else {
                $class = '\Widget\Grid\Toolbar\Toolbar';
            }
            $toolbar = new $class($toolbar + array('grid' => $grid));
        } elseif (is_object($toolbar)) {
            $toolbar -> setGrid($grid);
        } else {
            throw new \Exception('Invalid toolbar configuration');
        }
        return $toolbar;
    }

	/**
	 * @param \Widget\Grid\Grid $grid
	 * @return Toolbar
	 */
	public function setGrid(Grid $grid)
	{
		$this->_grid = $grid;
		return $this;
	}
	
	/**
	 * @return \Widget\Grid\Grid
	 */
	public function getGrid() 
	{
		return $this->_grid;
	}

    /**
     * @example
     * $applyAction = new Button(array(
     *       'hint' => 'Выполнить выбранное действие',
     *       'callback' => $this->getGrid()->getJavascriptObject().'.apply(\''.$this->getGrid()->getName().'\'); return false;',
     *       'icon' => 'check',
     *       'title' => 'Применить'
     * ));
     * $this->addButton($applyAction, 0);
     *
     * @param RenderInterface|array $button
     * @return Toolbar
     */
    public function addButton($button, $position = null)
    {
        $button = Button::factory($button);
        if ($position === null) {
            $this->_buttons[] = $button;
        } else {
            array_splice($this->_buttons, $position, null, array($button));
        }

        return $this;
    }

    /**
     * @param RenderInterface[] $buttons
     * @return Toolbar
     */
    public function setButtons($buttons)
    {
        $this->_buttons = array();
        foreach ($buttons as &$button) {
            $this->addButton($button);
        }
        return $this;
    }

    /**
     * @return RenderInterface[]
     */
    public function getButtons()
    {
        return $this->_buttons;
    }

    /**
     * @param RenderInterface[] $elements
     * @return Toolbar
     */
    public function setElements($elements)
    {
        $this->_elements = array();
        foreach ($elements as &$element) {
            $this->addElement($element);
        }
        return $this;
    }

    /**
     * @return RenderInterface[]
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * @param RenderInterface $element
     * @return Toolbar
     */
    public function addElement(RenderInterface $element)
    {
        $this->_elements[] = $element;
        return $this;
    }

	/**
	 * 'remove' => array(
	 *		'title' => 'Удалить',
	 *		'href'   => '/aaa/remove',
	 *		'question' => 'Вы действительно хотите удалить выбранные товары?',
	 *		'success' => 'Товары удалены',
	 *		'errors'  => 'При удалении товаров возникла ошибка',
	 *	),
	 *	'copyright' => array(
	 *		'title' => 'Поставить товар на описание',
	 *		'handler' => 'core.popup.show("popup-copyright","/shop/catalog/copyright/describe", {params : {products : selected}, modal : true})'
	 *	)
	 * 
	 * @param array|Action[] $actions
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
     * @return Action[]
     */
    public function getActions()
	{
		return $this->_actions;
	}
	
	/**
	 * addAction('remove', array(
     *      'title' => 'Удалить',
     *		'href'   => '/aaa/remove',
     *		'question' => 'Вы действительно хотите удалить выбранные товары?',
     *		'success' => 'Товары удалены',
     *		'errors'  => 'При удалении товаров возникла ошибка',
	 * ));
	 * 
	 * или 
	 * 
	 * addAction(remove, new Action(array(
	 *		'title' => 'Поставить товар на описание',
	 *		'handler' => 'core.popup.show("popup-copyright","/shop/catalog/copyright/describe", {params : {products : selected}, modal : true})'
	 * ))
	 *
     * @param string $name
	 * @param array|Action $action
	 * @return Toolbar
	 */
	public function addAction($name, $action)
	{
        $this->_actions[$name] = Action::factory($action);
		return $this;
	}

	/** 
	 * @inheritdoc
	 */
	public function render()
	{
		$count = $this->getGrid()->getStorage()->getCount();
		$html  = '<div class="grid-toolbar clearfix">';
			$html  .= '<div class="pull-left paginator">';
				$html  .= '<button data-role="tooltip" title="Обновить" id="refresh" onclick="'.$this->getGrid()->getJavascriptObject().'.load(); return false;" class="pull-left btn btn-warning btn-small"><i class="icon-refresh icon-white"></i></button>';
				$html  .= '<div id="number-in-page" class="pull-left">';
				if ($this->getGrid()->getSelection()) {
					$html  .= 'Выбрано: <strong data-role="selected">0</strong>&nbsp;&Iota;&nbsp;';
				}
				$html  .= 'Всего: <strong>'.$count.'</strong>';
				$html  .= '</div>';

                $html  .= '<div class="pull-left">';
                foreach ($this->getElements() as $element) {
                    $html  .= $element->render();
                }
                $html  .= '</div>';
			$html  .= '</div>';
		
		
			$html  .= '<div class="pull-right">';

                #actions
                $actions = $this->getActions();
				if (!empty($actions)) {
					$html  .= '<div class="pull-left">Действие:&nbsp;';
						$html  .= '<select class="additionally" style="margin-right: 10px; max-width:150px;" name="'.$this->getGrid()->getName().'_action">';
						$html .= '<option></option>';
						foreach ($actions as $key => &$action) {
							$html .= '<option value="'.$key.'" data-json="'.$action->toJson().'">'.$action->getTitle().'</option>';
						}						
						$html  .= '</select>';
					$html  .= '</div>';

                    $applyAction = new Button(array(
                        'hint' => 'Выполнить выбранное действие',
                        'callback' => $this->getGrid()->getJavascriptObject().'.apply(\''.$this->getGrid()->getName().'\'); return false;',
                        'icon' => 'check',
                        'title' => 'Применить'
                    ));
                    $this->addButton($applyAction, 0);
				}

                #buttons
				$html  .= '<div class="btn-group pull-right">';
                    foreach ($this->getButtons() as $button) {
                        $html .= $button->render();
                    }
				$html  .= '</div>';
			$html  .= '</div>';
		$html  .= '</div>';			
		return $html;
	}
}