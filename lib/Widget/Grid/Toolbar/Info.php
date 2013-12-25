<?php
namespace Widget\Grid\Toolbar;

use Widget\AbstractRenderer;
use Widget\Grid\Grid;

/**
 * Information about grid rows
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Info extends AbstractRenderer
{
    /**
     * @var Grid
     */
    protected $grid = null;

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Toolbar/info.html.twig';
    }

    /**
     * @return \Widget\Grid\Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param Grid $grid
     *
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        return $this;
    }
}
