<?php
namespace Widget\Grid\Filter;

/**
 * Select filter
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class SelectFilter extends TextFilter
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var Boolean
     */
    protected $multiselect = false;

    /**
     * Тип фильтра (integer|string)
     *
     * @var string
     */
    protected $type = 'integer';

    /**
     * @var bool
     */
    protected $empty = true;

    /**
     * @var callable
     */
    protected $generator;

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Filter/select.html.twig';
    }

    /**
     * <code>
     *   $tehnologyManager = $this->tehnologyManager;
     *   $generator = function () use ($tehnologyManager) {
     *       $tehnologies = $tehnologyManager->findAll();
     *       $options = array();
     *       foreach ($tehnologies as $tenhology) {
     *          $options[$tenhology->getId()] = $tenhology->getName();
     *       }
     *
     *       return $options;
     *   };
     *   $columnTehnology->setFilter($builder->createFilter('select', array('generator' => $generator)));
     * </code>
     *
     * @param callable $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param Boolean $multiselect
     *
     * @return SelectFilter
     */
    public function setMultiselect($multiselect)
    {
        $this->multiselect = $multiselect;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMultiselect()
    {
        return $this->multiselect;
    }

    /**
     * @param boolean $empty
     *
     * @return SelectFilter
     */
    public function setEmpty($empty)
    {
        $this->empty = $empty;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->empty;
    }

    /**
     * @param array $options
     *
     * @return SelectFilter
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if (!empty($this->generator) && is_callable($this->generator)) {
            $this->options = call_user_func($this->generator, array($this->value));
        }

        return parent::render();
    }
}
