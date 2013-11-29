<?php
namespace Widget;

/**
 * Abstract decorator class
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
abstract class AbstractDecorator
{
    /**
     * @var AbstractWidget
     */
    protected $element;

    /**
     * @param string $content
     *
     * @return string
     */
    abstract public function render($content);

    /**
     * @param AbstractWidget $element
     *
     * @return AbstractWidget
     */
    public function setElement(AbstractWidget $element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * @return AbstractWidget
     */
    public function getElement()
    {
        return $this->element;
    }
}
