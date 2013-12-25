<?php
namespace Widget\Grid\Extension;

use Widget\AbstractExtension;
use Widget\ObserverListener;

/**
 * Paginator
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Pagination extends AbstractExtension
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $topToolbar    = $this->getWidget()->getTopToolbar();
        $bottomToolbar = $this->getWidget()->getBottomToolbar();

        if ($bottomToolbar || $topToolbar) {
            $paginator = new PaginationModel($this->getWidget()->getStorage(), $this->getOptions());
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
