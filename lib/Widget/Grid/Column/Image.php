<?php
namespace Widget\Grid\Column;

/**
 * Image column
 *
 * @package Widget\Grid\Column
 */
class Image extends Column
{
    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'Column/image.html.twig';
    }
}
