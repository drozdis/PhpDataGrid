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
    private $onPage = 20;

    /**
     * @var array
     */
    private $onPageList = array(10, 20, 50, 100, 200);

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
     * @param array $onPageList
     *
     * @return $this
     */
    public function setOnPageList($onPageList)
    {
        $this->onPageList = $onPageList;

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
            $paginator->setOnPageList($this->onPageList);

            //lisener
            $lisener = new ObserverListener(function ($widget) use ($paginator) {
                $paginator->setURL($widget->getUrl());
            });
            $this->getWidget()->addEventListener('before_render', $lisener);

            //lisener
            $lisener = new ObserverListener(function ($widget) use ($paginator) {
                $paginator->setURL($widget->getUrl());
            });
            $this->getWidget()->addEventListener('before_render', $lisener);

            //страница
            $page = $this->getWidget()->getUrlParams('page');
            $paginator->setPage($page);
            $onPage = $this->getWidget()->getUrlParams('onpage');
            if ($onPage) {
                $paginator->setOnPage($onPage);
            }
            $this->getWidget()->getStorage()->setPage($page);
            $this->getWidget()->getStorage()->setOnPage($paginator->getOnPage());

            $topToolbar && $topToolbar->addElement($paginator);
            $bottomToolbar && $bottomToolbar->addElement($paginator);
        }
    }
}
