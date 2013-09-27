<?php
namespace Widget\Grid\Toolbar;
use Widget\AbstractWidget;
use Widget\Helper;
use Widget\RenderInterface;

/**
 * Клас "Тулбар"
 *
 * @package A1_Widget
 * @author drozd
 */
class Button implements RenderInterface
{
    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @var string
     */
    protected $_hint = '';

    /**
     * @var string
     */
    protected $_callback = null;

    /**
     * @var string
     */
    protected $_class = 'btn-warning';

    /**
     * @var string
     */
    protected $_icon = '';

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        Helper::setConstructorOptions($this, $options);
    }

    /**
     * @param $action
     * @return object|Button
     * @throws \Exception
     */
    public static function factory($button)
    {
        if (is_array($button)) {
            if (!empty($button['class']) && class_exists($button['class'])) {
                $class = $button['class'];
            } else {
                $class = '\Widget\Grid\Toolbar\Button';
            }
            $button = new $class($button);
        } elseif (is_object($button)) {

        } else {
            throw new \Exception('Invalid button configuration');
        }

        if (!($button instanceof \Widget\Grid\Toolbar\Button)) {
            throw new \Exception('Class of toolbar button should be instanced from \Widget\Grid\Toolbar\Button');
        }

        return $button;
    }

    /**
     * @param null $callback
     */
    public function setCallback($callback)
    {
        $this->_callback = $callback;
    }

    /**
     * @return null
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * @param string $hint
     */
    public function setHint($hint)
    {
        $this->_hint = $hint;
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->_hint;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->_class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->_icon = $icon;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        if ($icon = $this->getIcon()) {
            $icon = '<i class="icon-'.$icon.' icon-white"></i>';
        }
        return '<button data-role="tooltip" title="'.$this->getHint().'" class="btn btn-small '.$this->getClass().'" onclick="'.$this->getCallback().'">'.($icon ? $icon.' ' : '').$this->getTitle().'</button>';
    }
}
