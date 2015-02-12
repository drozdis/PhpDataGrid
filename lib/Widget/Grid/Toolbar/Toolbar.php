<?php
namespace Widget\Grid\Toolbar;

use Widget\AbstractRenderer;
use Widget\Grid\Grid;
use Widget\RenderInterface;

/**
 * Toolbar for grid
 *
 * It contains items:
 *  - elements implements RendererInterface
 *  - buttons  implements RendererInterface extends Widget\Grid\Toolbar\Button
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Toolbar extends AbstractRenderer
{
    const SERVICE_CONTAINER = 99999;

    /**
     * @var \Widget\Grid\Grid
     */
    protected $grid = null;

    /**
     * @var RenderInterface[]
     */
    protected $buttons = array();

    /**
     * @var RenderInterface[]
     */
    protected $actions = array();

    /**
     * @var \Widget\RenderInterface[]
     */
    protected $elements = array();

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
     * @param int             $position
     *
     * @return $this
     */
    public function addElement(RenderInterface $element, $position = null)
    {
        if ($position === null) {
            $this->elements[] = $element;
        } else {
            array_splice($this->elements, $position, null, array($element));
        }

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
     * @param string            $container
     *
     * @return $this
     */
    public function setButtons($buttons, $container = 0)
    {
        $this->buttons[$container] = array();
        foreach ($buttons as &$button) {
            $this->addButton($button, $container);
        }

        return $this;
    }

    /**
     * <code>
     *   $button = new Button();
     *   $button->setTitle('Filter');
     *   $button->setHint('Apply filter');
     *   $button->setCallback($this->getGrid()->getJavascriptObject() . '.doFilter(); return false;');
     *   $button->setIcon('filter');
     *   $this->addButton($button);
     * </code>
     *
     * @param RenderInterface $button
     * @param string          $container
     * @param int             $position
     *
     * @return $this
     */
    public function addButton(RenderInterface $button, $container = 0, $position = null)
    {
        if ($position === null) {
            $this->buttons[$container][] = $button;
        } else {
            array_splice($this->buttons[$container], $position, null, array($button));
        }

        //sort
        ksort($this->buttons);

        return $this;
    }

    /**
     * <code>
     *   $button = new ACtion();
     *   $button->setTitle('Filter');
     *   $button->setHint('Apply filter');
     *   $button->setCallback($this->getGrid()->getJavascriptObject() . '.doFilter(); return false;');
     *   $button->setIcon('filter');
     *   $this->addAction($button);
     * </code>
     *
     * @param RenderInterface $action
     * @param string          $container
     * @param int             $position
     *
     * @return $this
     */
    public function addAction(RenderInterface $action, $position = null)
    {
        if ($position === null) {
            $this->actions[] = $action;
        } else {
            array_splice($this->actions, $position, null, array($action));
        }

        //sort
        ksort($this->actions);

        return $this;
    }

    /**
     * @return \Widget\RenderInterface[]
     */
    public function getActions()
    {
        return $this->actions;
    }
}
