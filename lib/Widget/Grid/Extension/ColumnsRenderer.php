<?php
namespace Widget\Grid\Extension;

use Widget\AbstractRenderer;
use Widget\Grid\Grid;

/**
 * Window for columns extension
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class ColumnsRenderer extends AbstractRenderer
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'Extension\columns.html.twig';
    }
}
