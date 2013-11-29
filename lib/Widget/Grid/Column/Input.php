<?php
namespace Widget\Grid\Column;

/**
 * Колонка поле ввода
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Input extends Column
{
    /**
     * @var string
     */
    protected $idField = null;

    /**
     * Атрибуты input
     * @var array
     */
    protected $attrs = array();

    /**
     * Тип поля ввода name[id] или name_id
     */
    protected $type = '[]';

    /**
     * @param string $field
     *
     * @return Input
     */
    public function setIdField($field)
    {
        $this->idField = $field;

        return $this;
    }

    /**
     * @param array $attrs
     *
     * @return Input
     */
    public function setAttrs($attrs)
    {
        $this->attrs = $attrs;

        return $this;
    }

    /**
     * @param array $type
     *
     * @return Input
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        $value = parent::value($row);
        $idField = $this->getGrid()->getStorage()->getIdField();
        $attrs = array();
        if (!empty($this->attrs)) {
            foreach ($this->attrs as $key => $attr) {
                $attrs[] = $key . '="' . $attr . '"';
            }
        }
        $attrs = join(' ', $attrs);

        $name = $this->type == '[]' ? $this->getName() . '[' . $row[$this->idField ? $this->idField : $idField] . ']' : $this->getName() . '_' . $row[$this->idField ? $this->idField : $idField];

        return '<input ' . $attrs . ' type="text" class="input-text" name="' . $name . '" value="' . $value . '" />';
    }
}
