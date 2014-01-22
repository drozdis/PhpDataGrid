<?php
namespace Widget\Grid\Extension;

use Widget\AbstractExtension;
use Widget\ObserverListener;

/**
 * Fix grid header
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class FixHeader extends AbstractExtension
{
    /**
     * @var int
     */
    private $fixedOffset = 0;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->render();
    }

    /**
     * @param int $fixedOffset
     *
     * @return $this
     */
    public function setFixedOffset($fixedOffset)
    {
        $this->fixedOffset = $fixedOffset;

        return $this;
    }

    /**
     * @return int
     */
    public function getFixedOffset()
    {
        return $this->fixedOffset;
    }

    /**
     * Render extension
     */
    private function render()
    {
        $self = $this;

        $listener = new ObserverListener(function ($grid, &$content) use ($self) {
            $renderer = new FixHeaderRenderer($grid, $self);
            $content .= $renderer->render();
        });
        $this->getWidget()->addEventListener('after_render', $listener);
    }
}
