<?php
namespace Widget\Grid\Toolbar;
use Widget\AbstractRenderer;

/**
 * Toolbar button
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Button extends AbstractRenderer
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $hint = '';

    /**
     * @var string
     */
    protected $callback = null;

    /**
     * @var string
     */
    protected $class = 'btn-warning';

    /**
     * @var string
     */
    protected $icon = '';

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Toolbar/button.html.twig';
    }

    /**
     * @param null $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $hint
     */
    public function setHint($hint)
    {
        $this->hint = $hint;
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }
}
