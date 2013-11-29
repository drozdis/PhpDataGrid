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
    protected function value($row)
    {
        return preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/", " ", sprintf('%01.2f', (float) parent::value($row)));
    }
}
