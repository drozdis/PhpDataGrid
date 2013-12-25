<?php
namespace Widget;

/**
 * Widget abstract class
 * It implements standard functionality
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
abstract class AbstractWidget extends AbstractRenderer
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var AbstractWidget
     */
    protected $parent = null;

    /**
     * @var AbstractExtension[]
     */
    protected $extensions = array();

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
     * @return $this
     */
    protected function init()
    {
        return $this;
    }

    /**
     * @param string|null $name
     *
     * @return $this
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
     * @return $this
     */
    public function setParent(AbstractWidget $parent)
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
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return AbstractExtension[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @param AbstractExtension[] $extensions
     *
     * @return $this
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
