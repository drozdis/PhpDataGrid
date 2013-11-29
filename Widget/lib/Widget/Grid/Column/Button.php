<?php
namespace Widget\Grid\Column;

/**
 * Button
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Button extends Column
{
    /**
     * Обрабочик кнопки
     * @var string
     */
    protected $handler = '';

    /**
     * @var string
     */
    protected $type = 'primary';

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $handler
     *
     * @return Button
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Button
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
        $idField = $this->getGrid()->getStorage()->getIdField();

        return '<button type="button" class="btn btn-sm btn-' . $this->getType() . '" name="' . $this->getName() . '" value="' . $row[$idField] . '" ' . ($this->getHandler() ? 'onclick="' . $this->getHandler() . '"' : '') . ' >' . $this->getTitle() . '</button>';
    }
}
