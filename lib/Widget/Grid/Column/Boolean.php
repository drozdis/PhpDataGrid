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
    public function getValue()
    {
        return parent::getValue() > 0 ? 'yes' : 'no';
    }
}
