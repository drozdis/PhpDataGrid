<?php
namespace Widget;

/**
 * Class AbstractExtension
 *
 * @package Widget
 */
abstract class AbstractExtension
{				
	/** 
	 * @var WidgetAbstract
	 */
	protected $_widget = null;
	
	/**
	 * @param Array $options
	 */
	public function __construct($options = array())
	{
		Helper::setConstructorOptions($this, $options);
	}

    /**
     * @param Array $extension
     * @return mixed
     * @throws Exception
     */
    public static function factory($extension, $widget)
    {
        if (is_object($extension)) {
            return $extension;
        }

        if (is_array($extension)) {
            if (!empty($extension['class']) && class_exists($extension['class'])) {
                $class = $extension['class'];
            } elseif (!empty($extension['class']) && class_exists('Widget\Grid\Extension\\'.ucfirst($extension['class']))) {
                $class = 'Widget\Grid\Extension\\'.ucfirst($extension['class']);
            }
        } elseif (is_string($extension)) {
            if (class_exists($extension)) {
                $class = $extension;
            } elseif (class_exists('Widget\Grid\Extension\\'.ucfirst($extension))) {
                $class = 'Widget\Grid\Extension\\'.ucfirst($extension);
            }
        }
        if (!isset($class)) {
            throw new \Exception('Invalid extension configuration');
        }

        #create extension
        $extension = new $class(is_string($extension) ? array() : $extension);

        #init
        $extension -> setWidget($widget) -> init();

        return $extension;
    }

		
	/**
	 * @param AbstractWidget $widget
	 * @return AbstractExtension
	 */
	public function setWidget(AbstractWidget $widget)
	{
		$this->_widget = $widget;
		return $this;
	}
	
	/**
	 * @return AbstractWidget
	 */
	public function getWidget() 
	{
		return $this->_widget;
	}
	
	/**
	 * initialization
	 */
	abstract public function init();
}