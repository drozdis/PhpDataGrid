<?php
namespace Widget\Grid\Toolbar;

use Widget\Helper;

/**
 * Toolbar builder
 */
class ToolbarBuilder
{
    const ACTION_CLASS = '\Widget\Grid\Toolbar\Action';

    const BUTTON_CLASS = '\Widget\Grid\Toolbar\Button';

    /**
     * @var \Widget\Grid\Toolbar\Toolbar
     */
    private $toolbar;

    /**
     * @return \Widget\Grid\Toolbar\Toolbar
     */
    public function getToolbar()
    {
        return $this->toolbar;
    }

    /**
     * @param \Widget\Grid\Toolbar\Toolbar|string $type
     * @param array                               $options
     */
    public function __construct($type, $options = array())
    {
        if (is_object($type)) {
            $this->toolbar = $type;
        } elseif (is_string($type)) {
            if (class_exists($type)) {
                $class = $type;
            } elseif (class_exists('\Widget\Grid\Toolbar\\' . ucfirst($type))) {
                $class = '\Widget\Grid\Toolbar\\' . ucfirst($type);
            } else {
                throw new \Exception('Unknown class ' . $type);
            }
            $this->toolbar = new $class();
        } else {
            throw new \Exception('Unknown configuration');
        }

        //apply options
        \Widget\Helper::setConstructorOptions($this->toolbar, $options);
    }

    /**
     * Create toolbar action
     *
     * @param $name
     * @param $options
     *
     * @return ToolbarBuilder
     */
    public function addAction($name, $options)
    {
        $class = self::ACTION_CLASS;
        $action = new $class();

        //apply options
        \Widget\Helper::setConstructorOptions($action, $options);

        //set name
        $action->setName($name);

        $this->toolbar->addAction($action);
    }

    /**
     * Create toolbar button
     *
     * @param $name
     * @param $options
     *
     * @return ToolbarBuilder
     */
    public function addButton($options)
    {
        $class = self::BUTTON_CLASS;
        $button = new $class();

        //apply options
        \Widget\Helper::setConstructorOptions($button, $options);

        $this->toolbar->addButton($button);

        return $this;
    }
}
