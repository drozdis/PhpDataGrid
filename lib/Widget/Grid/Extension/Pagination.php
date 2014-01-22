<?php
namespace Widget\Grid\Extension;

use Widget\AbstractExtension;
use Widget\ObserverListener;

/**
 * Paginator
 * //@todo ahother option from renderer
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Pagination extends AbstractExtension
{
    /**
     * @var int
     */
    private $onPage = 0;

    /**
     * @param int $onPage
     *
     * @return $this
     */
    public function setOnPage($onPage)
    {
        $this->onPage = $onPage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $topToolbar    = $this->getWidget()->getTopToolbar();
        $bottomToolbar = $this->getWidget()->getBottomToolbar();

        if ($bottomToolbar || $topToolbar) {
            $paginator = new PaginationRenderer($this->getWidget()->getStorage());
            $paginator->setOnPage($this->onPage);
            $paginator->setParams(array('grid' => $this->getWidget()));

            //lisener
            $lisener = new ObserverListener(function ($widget) use ($paginator) {
                $paginator->setURL($widget->getUrl());
            });
            $this->getWidget()->addEventListener('before_render', $lisener);

            //страница
            $page = $this->getWidget()->getUrlParams('page');
            $paginator->setPage($page);
            $this->getWidget()->getStorage()->setPage($page);
            $this->getWidget()->getStorage()->setOnPage($paginator->getOnPage());

            $topToolbar && $topToolbar->addElement($paginator);
            $bottomToolbar && $bottomToolbar->addElement($paginator);
        }
    }
}
