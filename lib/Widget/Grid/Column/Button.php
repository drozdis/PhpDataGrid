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
     * Javascript handler
     *
     * @var string
     */
    private $handler = '';

    /**
     * Icon
     *
     * @var string
     */
    private $type = 'primary';

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
