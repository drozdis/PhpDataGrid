<?php
namespace Widget\Grid\Filter;

/**
 * Text filter
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class TextFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Filter/text.html.twig';
    }
}
