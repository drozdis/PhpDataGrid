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
     * @var bool
     */
    protected $empty = true;

    /**
     * @return Boolean
     */
    public function getMultiselect()
    {
        return $this->multiselect;
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
        $column = $this->getColumn()->getName();
        $grid = $this->getGrid();
        $options = $this->getOptions();

        if ($this->getMultiselect()) {
            $html = '<div class="field-100"><div class="multiselect nowrap" style="height:60px;">';
            foreach ($options as $key => $value) {
                $html .= '<label class="checkbox"><input name="' . $column . '[]" onclick="' . $grid->getJavascriptObject() . '.doFilter();" type="checkbox" id="' . $column . $key . '" value="' . $key . '" ' . ($this->getValue() !== null && $this->getValue() !== '' && in_array($key, (array) $this->getValue()) ? 'checked="checked"' : '') . ' /> ' . $value . '</label>';
            }
            $html .= '</div></div>';
        } else {
            $html = '<div class="field-100">';
            $html .= '<select class="form-control" name="' . $column . '" onchange="' . $grid->getJavascriptObject() . '.doFilter();">';
            $this->empty && $html .= '<option value=""></option>';
            foreach ($options as $key => $value) {
                $html .= '<option value="' . $key . '" ' . ($this->getValue() !== null && $this->getValue() !== '' && in_array($key, (array) $this->getValue()) ? 'selected="selected"' : '') . '>' . $value . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';
        }

        return $html;
    }
}
