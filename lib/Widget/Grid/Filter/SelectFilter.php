<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Выпадающий список)
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
     * {@inheritdoc}
     */
    public function render()
    {
        $column = $this->getColumn()->getName();
        $grid = $this->getGrid();

        if (!empty($this->generator) && is_callable($this->generator)) {
            $this->options = call_user_func($this->generator, array($this->value));
        }

        if ($this->multiselect) {
            $html = '<div class="field-100"><div class="multiselect nowrap" style="height:60px;">';
            foreach ($this->options as $key => $value) {
                $html .= '<label class="checkbox"><input name="' . $column . '[]" onclick="' . $grid->getJavascriptObject() . '.doFilter();" type="checkbox" id="' . $column . $key . '" value="' . $key . '" ' . ($this->getValue() !== null && $this->getValue() !== '' && in_array($key, (array) $this->getValue()) ? 'checked="checked"' : '') . ' /> ' . $value . '</label>';
            }
            $html .= '</div></div>';
        } else {
            $html = '<div class="field-100">';
            $html .= '<select class="form-control" name="' . $column . '" onchange="' . $grid->getJavascriptObject() . '.doFilter();">';
            $this->empty && $html .= '<option value=""></option>';
            foreach ($this->options as $key => $value) {
                $html .= '<option value="' . $key . '" ' . ($this->getValue() !== null && $this->getValue() !== '' && in_array($key, (array) $this->getValue()) ? 'selected="selected"' : '') . '>' . $value . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';
        }

        return $html;
    }
}
