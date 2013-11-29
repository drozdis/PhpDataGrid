<?php
namespace Widget\Grid\Column;

/**
 * Column checkbox
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Checkbox extends Column
{
    /**
     * Обрабочик чекбокса
     * @var string
     */
    protected $handler = '';

    /**
     * Название поля
     * @var string
     */
    protected $inputName = '';

    /**
     * Выкл чекбоксы
     * @var array
     */
    protected $disabled = array();

    /**
     * @param string $handler
     *
     * @return Checkbox
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @param array $disabled
     *
     * @return Checkbox
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getInputName()
    {
        return $this->inputName ? $this->inputName : $this->getName();
    }

    /**
     * @param string $inputName
     *
     * @return Checkbox
     */
    public function setInputName($inputName)
    {
        $this->inputName = $inputName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        $idField = $this->getGrid()->getStorage()->getIdField();
        $checked = parent::value($row) > 0 ? true : false;
        $id = \Widget\Helper::getValue($row, $idField);
        $disabled = in_array($id, $this->disabled);
        $html = '<input type="checkbox" name="' . $this->getInputName() . '" value="' . $id . '" ' . ($checked ? 'checked="checked"' : '') . ' ' . ($disabled ? 'disabled="disabled"' : '') . ' ' . ($this->getHandler() ? 'onchange="' . $this->getHandler() . '"' : '') . '  />';
        if ($disabled) {
            $html .= '<input type="hidden" name="' . $this->getInputName() . '" value="' . $id . '" />';
        }

        return $html;
    }
}
