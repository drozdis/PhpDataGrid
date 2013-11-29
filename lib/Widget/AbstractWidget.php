<?php
namespace Widget;

/**
 * Widget abstract class
 * It implements standard functionality
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
abstract class AbstractWidget extends ObserverAbstract implements RenderInterface
{
    /**
     * @var string
     */
    private static $resourceManagerClass = '\Widget\ResourceManager';

    /**
     * Уникальный идентификатор
     * @var string
     */
    protected $name = '';

    /**
     * Родитель
     * @var AbstractWidget
     */
    protected $parent = null;

    /**
     * Декораторы
     * @var AbstractDecorator[]
     */
    protected $decorators = array();

    /**
     * @var AbstractExtension[]
     */
    protected $extensions = array();

    /**
     * @var Null|Integer
     */
    protected $width = null;

    /**
     * @var ResourceManagerInterface
     */
    private $resourceManager = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->init();

        //установка имени
        if (empty($this->name)) {
            $this->setName(str_replace('\\', '_', get_class($this)));
        }
    }

    /**
     * Инициализация
     * @return AbstractWidget
     */
    protected function init()
    {
        return $this;
    }

    /**
     * @param string|null $name
     *
     * @return AbstractWidget
     */
    public function getParent($name = null)
    {
        if ($name === null) {
            return $this->parent;
        } elseif ($this->getName() == $name) {
            return $this;
        } elseif ($parent = $this->getParent()) {
            return $parent->getParent($name);
        }

        return null;
    }

    /**
     * @param AbstractWidget $parent
     *
     * @return AbstractWidget
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return AbstractWidget
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param Integer $width
     *
     * @return AbstractWidget
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @param AbstractDecorator $decorator
     *
     * @return AbstractWidget
     */
    public function addDecorator(AbstractDecorator $decorator)
    {
        $this->decorators[] = $decorator;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Render
     * @return string
     */
    public function render()
    {
        try {
            //event
            $this->fireEvent('before_render', array('widget' => $this));

            $content = $this->initialHtml();
            foreach ($this->getDecorators() as $name) {
                $decorator = $this->createDecorator($name);
                /* @var $decorator AbstractDecorator */
                $decorator->setElement($this);
                $content = $decorator->render($content);
            }
            //event
            $this->fireEvent('after_render', array('widget' => $this));
        } catch (\Exception $e) {
            $content = $e . '';
        }

        return $content;
    }

    /**
     * Базовый HTML для отображения, на который будут накладываться декораторы
     * @return string
     */
    protected function initialHtml()
    {
        return '';
    }

    /**
     * @return array
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * @param array $decorators
     *
     * @return AbstractWidget
     */
    public function setDecorators($decorators)
    {
        $this->decorators = $decorators;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return AbstractDecorator
     */
    public function createDecorator($name)
    {
        return new $name();
    }

    /**
     * @return \Widget\ResourceManagerInterface
     */
    public function getResourceManager()
    {
        if ($this->resourceManager === null) {
            $class = self::getResourceManagerClass();
            $this->resourceManager = new $class();
        }

        return $this->resourceManager;
    }

    /**
     * @return string
     */
    public static function getResourceManagerClass()
    {
        return self::$resourceManagerClass;
    }

    /**
     * @param string $resourceManagerClass
     */
    public static function setResourceManagerClass($resourceManagerClass)
    {
        self::$resourceManagerClass = $resourceManagerClass;
    }

    /**
     * @return AbstractExtension[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Установить Плагины
     * @param AbstractExtension[] $extensions
     *
     * @return AbstractWidget
     */
    public function setExtensions($extensions)
    {
        $this->extensions = array();
        foreach ($extensions as &$extension) {
            $this->addExtension($extension);
        }

        return $this;
    }

    /**
     * @param AbstractExtension $extension
     *
     * @return $this
     */
    public function addExtension(AbstractExtension $extension)
    {
        $extension->setWidget($this);
        $extension->init();
        $this->extensions[] = $extension;

        return $this;
    }
}
