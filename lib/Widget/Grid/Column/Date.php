<?php
namespace Widget\Grid\Column;

/**
 * Date column
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
     * @return $this
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
    public function getValue()
    {
        $value = parent::getValue();
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
