<?php
namespace Widget\Grid\Extension;
use Widget\AbstractExtension;
use Widget\Grid\Extension\Pagination\ModelAdapter;
use Widget\ObserverListener;


/**
 * Paginator
 * 
 * @package Widget\Grid\Extension
 * @author drozd
 */
class Pagination extends AbstractExtension
{
    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $topToolbar = $this->getWidget()->getTopToolbar();
        $bottomToolbar = $this->getWidget()->getBottomToolbar();

        if ($bottomToolbar || $topToolbar) {
            $paginator = new PaginationModel($this->getWidget()->getStorage(), $this->getOptions());
            $paginator -> setParams(array('grid' => $this->getWidget()));
            $paginator -> setURL($this->getWidget()->getUrl());

            #страница
            $page = $this->getWidget()->getUrlParams('page');
                $paginator->setPage($page);
                $this->getWidget()->getStorage()->setPage($page);
                $this->getWidget()->getStorage()->setOnPage($paginator->getOnPage());

            $topToolbar && $topToolbar->addElement($paginator);
            $bottomToolbar &&$bottomToolbar->addElement($paginator);
        }
    }
}