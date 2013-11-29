<?php
namespace Widget\Grid\Toolbar;

use Widget\Grid\Grid;
use Widget\RenderInterface;

/**
 * Клас "Тулбар"
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Toolbar implements RenderInterface
{
    /**
     * @var \Widget\Grid\Grid
     */
    protected $grid = null;

    /**
     * @var RenderInterface[]
     */
    protected $buttons = array();

    /**
     * @var \Widget\RenderInterface[]
     */
    protected $elements = array();

    /**
     * @var Action[]
     */
    protected $actions = array();

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $count = $this->getGrid()->getStorage()->getCount();
        $html = '<div class="grid-toolbar clearfix">';
        $html .= '<div class="pull-left paginator">';
        $html .= '<button data-toggle="tooltip" title="Обновить" id="refresh" onclick="' . $this->getGrid()->getJavascriptObject() . '.load(); return false;" class="pull-left btn btn-warning btn-sm"><i class="icon-refresh icon-white"></i></button>';
        $html .= '<div id="number-in-page" class="pull-left">';
        if ($this->getGrid()->isSelection()) {
            $html .= 'Всего: <strong data-role="selected">0</strong>&nbsp;&Iota;&nbsp;';
        }
        $html .= 'Rows: <strong>' . $count . '</strong>';
        $html .= '</div>';

        $html .= '<div class="pull-left">';
        foreach ($this->getElements() as $element) {
            $html .= $element->render();
        }
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="pull-right">';

        //actions
        $actions = $this->getActions();
        if (!empty($actions)) {
            $html .= '<div class="pull-left">Actions:&nbsp;';
            $html .= '<select class="additionally" style="margin-right: 10px; max-width:150px;" name="' . $this->getGrid()->getName() . '_action">';
            $html .= '<option></option>';
            foreach ($actions as $key => &$action) {
                $html .= '<option value="' . $key . '" data-json="' . $action->toJson() . '">' . $action->getTitle() . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            $applyAction = new Button();
            $applyAction->setTitle('Выполнить');
            $applyAction->setCallback($this->getGrid()->getJavascriptObject() . '.apply(\'' . $this->getGrid()->getName() . '\'); return false;');
            $applyAction->setIcon('check');
            $applyAction->setHint('Выполнить выбранное действие');
            $this->addButton($applyAction, 0);
        }

        //buttons
        $html .= '<div class="btn-group pull-right">';
        foreach ($this->getButtons() as $button) {
            $html .= $button->render();
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @return \Widget\Grid\Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param \Widget\Grid\Grid $grid
     *
     * @return Toolbar
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @return RenderInterface[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param RenderInterface[] $elements
     *
     * @return Toolbar
     */
    public function setElements($elements)
    {
        $this->elements = array();
        foreach ($elements as &$element) {
            $this->addElement($element);
        }

        return $this;
    }

    /**
     * @param RenderInterface $element
     *
     * @return Toolbar
     */
    public function addElement(RenderInterface $element)
    {
        $this->elements[] = $element;

        return $this;
    }

    /**
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action[] $actions
     *
     * @return Toolbar
     */
    public function setActions($actions)
    {
        $this->actions = array();
        foreach ($actions as &$action) {
            $this->addAction($action);
        }

        return $this;
    }

    /**
     * $action = new Action();
     * $action -> setName('remove');
     * $action -> setName('title', 'Удалить');
     * $action -> setHandler('core.popup.show("popup-copyright","/shop/catalog/copyright/describe", {params : {products : selected}, modal : true})');
     * ...
     * $toolbar -> addAction($action);
     *
     * @param Action $action
     *
     * @return Toolbar
     */
    public function addAction(Action $action)
    {
        $this->actions[$action->getName()] = $action;

        return $this;
    }

    /**
     * @return RenderInterface[]
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param RenderInterface[] $buttons
     *
     * @return Toolbar
     */
    public function setButtons($buttons)
    {
        $this->buttons = array();
        foreach ($buttons as &$button) {
            $this->addButton($button);
        }

        return $this;
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
     * @param RenderInterface $button
     *
     * @return Toolbar
     */
    public function addButton(RenderInterface $button, $position = null)
    {
        if ($position === null) {
            $this->buttons[] = $button;
        } else {
            array_splice($this->buttons, $position, null, array($button));
        }

        return $this;
    }
}
