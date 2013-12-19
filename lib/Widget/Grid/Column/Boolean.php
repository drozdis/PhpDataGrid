<?php
namespace Widget\Grid\Column;

/**
 * Column yes/no
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Boolean extends Column
{
    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        return parent::value($row) > 0 ? 'yes' : 'no';
    }
}
