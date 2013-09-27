<?php
namespace Widget;

/**
 * Абстрактный клас элемента
 * 
 * @package Widget
 * @author drozd
 */
abstract class AbstractWidget extends ObserverAbstract implements RenderInterface
{	
	/**
	 * Уникальный идентификатор
	 * @var String
	 */
	protected $_name = '';
	
	/**
	 * Родитель
	 * @var AbstractWidget
	 */
	protected $_parent = null;

	/**
	 * Декораторы
	 * @var AbstractDecorator[]
	 */
	protected $_decorators = array();
	
	/**
	 * @var AbstractExtension[]
	 */
	protected $_extensions = array();
	
	/**
	 * @var Null|Integer
	 */
	protected $_width = null;

    /**
     * @var ResourceManagerInterface
     */
    private $_resourceManager = null;

    /**
     * @var string
     */
    private static $_resourceManagerClass = '\Widget\ResourceManager';

	/**
	 * @param Array $options
	 */
	public function __construct($options = array())
	{
		Helper::setConstructorOptions($this, $options);
		$this->_init();
		
		#установка имени
		if (empty($this->_name)) {
			$this->setName(get_class($this));
		}
	}
	
	/**
	 * Инициализация
	 * @return AbstractWidget
	 */
    protected function _init() 
    {
    	return $this;
    }

    /**
     * @return \Widget\ResourceManagerInterface
     */
    public function getResourceManager()
    {
        if ($this->_resourceManager === null) {
            $class = self::getResourceManagerClass();
            $this->_resourceManager = new $class();
        }
        return $this->_resourceManager;
    }

    /**
     * @param string $resourceManagerClass
     */
    public static function setResourceManagerClass($resourceManagerClass)
    {
        self::$_resourceManagerClass = $resourceManagerClass;
    }

    /**
     * @return string
     */
    public static function getResourceManagerClass()
    {
        return self::$_resourceManagerClass;
    }

	/**
     * @return AbstractWidget
     */
    public function getParent($name = null)
    {
    	if ($name === null) {
    		return $this->_parent;
    	} elseif ($this->getName() == $name) {
    		return $this;
    	} elseif ($parent = $this->getParent()) {
    		return $parent->getParent($name);
    	}
    	return null;
    }
    
	/**
     * @param AbstractWidget $parent
     * @return AbstractWidget
     */
    public function setParent($parent)
    {
    	$this->_parent = $parent;
    	return $this;
    }
       
    /**
     * @return String
     */
    public function getName()
    {
    	return $this->_name;
    }
    
	/**
     * @param String $name
     * @return AbstractWidget
     */
    public function setName($name)
    {
    	$this->_name = $name;
    	return $this;
    }
    
    /**
     * @example 6 или 2, суммарно должно быть 12
     * @see http://twitter.github.com/bootstrap/scaffolding.html#fluidGridSystem
     *
     * @param Integer $width
     * @return A1_Widget_Form_Columns
     */
    public function setWidth($width)
    {
    	$this->_width = $width;
    	return $this;
    }
    
    /**
     * @return Integer
     */
    public function getWidth()
    {
    	return $this->_width;
    }

	/**
     * @return Array
     */
    public function getDecorators()
    {
    	return $this->_decorators;
    }
    
	/**
     * @param Array $decorators
     * @return AbstractWidget
     */
    public function setDecorators($decorators)
    {
    	$this->_decorators = $decorators;
    	return $this;
    }
    
    /**
     * @param String $name
     * @return AbstractWidget
     */
    public function addDecorator($decorator)
    {
    	$this->_decorators[] = $decorator;
    }
    
    /**
     * @param String $name
     */
    public function createDecorator($name)
    {
    	return new $name();
    }
        
    /**
     * Базовый HTML для отображения, на который будут накладываться декораторы
     * @return String
     */
    protected function _initialHtml()
    {
    	return '';
    }
    
    /**
     * Render
     * @return string
     */
    public function render()
    {
    	try {
	        $content = $this->_initialHtml();
	        foreach ($this->getDecorators() as $name) {
	        	$decorator = $this->createDecorator($name);
	            $decorator -> setElement($this);
	            $content = $decorator->render($content);
	        }
            $content .= $this->getResourceManager()->render();
    	} catch (Exception $e) {
    		$content = $e.'';
    	}
        return $content;
    }
	
	/**
     * (non-PHPdoc)
     * @see A1_Widget_Interface::render()
     */
	public function __toString()
	{
		return $this->render();
	}
	
	/**
	 * Установить Плагины
	 * @param AbstractExtension[] $extensions
	 * @return AbstractWidget
	 */
	public function setExtensions($extensions)
	{
        $this->_extensions = array();
        foreach ($extensions as &$extension) {
            $this->addExtension($extension);
        }
		return $this;
	}

    /**
     * @param AbstractExtension|array $extension
     * @return $this
     */
    public function addExtension($extension)
	{
		$this->_extensions[] = AbstractExtension::factory($extension, $this);
		return $this;
	}
	
	/**
	 * @return AbstractExtension[]
	 */
	public function getExtensions()
	{
		return $this->_extensions;
	}
}