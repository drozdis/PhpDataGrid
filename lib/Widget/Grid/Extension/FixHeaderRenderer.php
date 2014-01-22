<?php
namespace Widget\Grid\Extension;

use Widget\AbstractExtension;
use Widget\AbstractRenderer;
use Widget\Grid\Grid;

/**
 * Renderer for fix header extension
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class FixHeaderRenderer extends AbstractRenderer
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @var FixedHeader
     */
    private $extension;

    /**
     * @param Grid              $grid
     * @param AbstractExtension $extention
     */
    public function __construct(Grid $grid, AbstractExtension $extention)
    {
        $this->grid      = $grid;
        $this->extension = $extention;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @return AbstractExtension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'Extension\fixheader.html.twig';
    }
}
