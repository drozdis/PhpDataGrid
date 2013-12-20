<?php
namespace Widget\Grid\Filter;

use Widget\Grid\Storage\AbstractStorage;

/**
 * Date range filter
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class DateRangeFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Filter/dateRange.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(AbstractStorage $store)
    {
        $value = $this->getValue();
        if (!empty($value)) {
            $from = isset($value['from']) ? $this->convertDateFormat($value['from']) . ' 0:00:00' : '';
            $to   = isset($value['to']) ? $this->convertDateFormat($value['to']) . ' 23:59:59' : '';

            $from != '' && $store->addFilter($this->getColumn()->getName() . '_from', $this->getField(), $from, ' >= ?');
            $to != '' && $store->addFilter($this->getColumn()->getName() . '_to', $this->getField(), $to, ' <= ?');
        }

        return $this;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function convertDateFormat($value)
    {
        $date = new \DateTime($value);

        return $date->format('y-m-d');
    }
}
