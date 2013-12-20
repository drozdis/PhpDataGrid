<?php
namespace Widget\Grid\Toolbar;

use Widget\AbstractRenderer;
use Widget\Grid\Grid;
use Widget\RenderInterface;

/**
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Toolbar extends AbstractRenderer
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
    public function getTemplate()
    {
        return 'Toolbar/toolbar.html.twig';
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
