<?php
namespace Widget\Grid\Column;

/**
 * Format price
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Price extends Column
{
    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/", " ", sprintf('%01.2f', (float) parent::getValue()));
    }
}
