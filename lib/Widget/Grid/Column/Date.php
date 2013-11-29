<?php
namespace Widget\Grid\Column;

/**
 * Колонка дата
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Date extends Column
{
    /**
     * @var string
     */
    protected $format = 'd.m.Y';

    /**
     * @param string $format
     *
     * @return Date
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        $value = parent::value($row);
        if (!empty($value)) {
            if (!is_object($value)) {
                $value = new \DateTime($value);
            }
            if ($value) {
                return $value -> format($this->getFormat());
            }
        }

        return '';
    }
}
