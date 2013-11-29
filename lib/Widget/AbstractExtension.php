<?php
namespace Widget;

/**
 * Abstract extension class
 * It implements standard extension functionality
 */
abstract class AbstractExtension
{
    /**
     * @var AbstractWidget
     */
    protected $widget = null;

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
