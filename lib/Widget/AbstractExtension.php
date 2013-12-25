<?php
namespace Widget;

/**
 * Abstract extension class
 * It implements standard extension functionality
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
abstract class AbstractExtension
{
    /**
     * @var AbstractWidget
     */
    protected $widget = null;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param AbstractWidget $widget
     *
     * @return AbstractExtension
     */
    public function setWidget(AbstractWidget $widget)
    {
        $this->widget = $widget;

        return $this;
    }

    /**
     * @return AbstractWidget
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * initialization
     */
    abstract public function init();
}
