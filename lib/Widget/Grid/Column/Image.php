<?php
namespace Widget\Grid\Column;

/**
 * Колонка изображение
 * @package Widget\Grid\Column
 */
class Image extends Column
{
    /**
     * @var array
     */
    protected $decorators = array('resize' => array('width' => 50, 'height' => 50));

    /**
     * @param array $decorators
     *
     * @return Image
     */
    public function setDecorators($decorators)
    {
        $this->decorators = $decorators;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        $value = parent::value($row);
        if (!empty($value)) {
            return $this->getView()->image($value, $this->decorators);
        }

        return '';
    }
}
