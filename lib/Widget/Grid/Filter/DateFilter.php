<?php
namespace Widget\Grid\Filter;

use Widget\Grid\Storage\AbstractStorage;

/**
 * Date filter
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class DateFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Filter/dange.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(AbstractStorage $store)
    {
        $value = $this->getValue();
        if (!empty($value)) {
            $date = new \DateTime($value);

            $store->addFilter($this->getColumn()->getName(), $this->getField(), $date->format('y-m-d'), ' = ?');
        }

        return $this;
    }
}
