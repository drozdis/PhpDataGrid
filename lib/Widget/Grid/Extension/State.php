<?php
namespace Widget\Grid\Extension;

use Widget\AbstractExtension;
use Widget\Grid\Extension\State\AbstractAdapter;
use Widget\Grid\Extension\State\SessionAdapter;
use Widget\ObserverListener;

/**
 * Grid extension for save state
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class State extends AbstractExtension
{
    /**
     * @var AbstractAdapter
     */
    private $stateAdapter;

    /**
     * @param AbstractAdapter $stateAdapter
     */
    public function setStateAdapter(AbstractAdapter $stateAdapter)
    {
        $this->stateAdapter = $stateAdapter;
    }

    /**
     * @return AbstractAdapter
     */
    public function getStateAdapter()
    {
        if (is_null($this->stateAdapter)) {
            $this->stateAdapter = new SessionAdapter($this->getWidget()->getName());
        }

        return $this->stateAdapter;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $self = $this;

        //save state
        $afterRenderListener = new ObserverListener(function ($grid) use ($self) {
            $self->saveState($grid);
        });
        $this->getWidget()->addEventListener('after_render', $afterRenderListener);

        //apply state
        $afterBuildListener = new ObserverListener(function ($grid) use ($self) {
            $self->applyState($grid);
        });
        $this->getWidget()->addEventListener('after_build', $afterBuildListener);
    }

    /**
     * @param Grid $grid
     *
     * @return $this
     */
    public function saveState($grid)
    {
        $state = array(
            'columns' => array(),
            'filters' => array(),
            'orders'  => array(),
            'storage' => array()
        );

        //columns and filters state
        foreach ($grid->getColumns() as $name => $column) {
            $columnState             = & $state['columns'][$name];
            $columnState['hidden']   = $column->isHidden();
            $columnState['position'] = $column->getPosition();

            if (($filter = $column->getFilter()) && $filter->isState()) {
                $state['filters'][$name] = $filter->getValue();
            }
        }

        //storage
        if ($storage = $grid->getStorage()) {
            /* @var $storage \Widget\Grid\Storage\AbstractStorage */

            $ordersState = & $state['orders'];
            foreach ($grid->getColumns() as $column) {
                if ($column->isHidden() == false && $column->isSortable() && $storage->isOrder($column->getField())) {
                    $ordersState[$column->getField()] = $storage->isOrder($column->getField());
                }
            }

            $state['storage']['page']   = $storage->getPage();
            $state['storage']['onpage'] = $storage->getOnPage();
        }

        //save state
        $this->getStateAdapter()->setState($state);

        return $this;
    }

    /**
     * @param Grid $grid
     *
     * @return $this
     */
    public function applyState($grid)
    {
        $state = $this->getStateAdapter()->getState();

        //order, filters and storage
        $params = $grid->getUrlParams();
        if (empty($params['order']) && empty($params['filter'])) {

            !empty($state['filters']) && $grid->applyFilter($state['filters']);
            !empty($state['orders'])  && $grid->applyOrder($state['orders']);

            if ($grid->getStorage()) {
                !empty($state['storage']['page']) && $grid->getStorage()->setPage((int) $state['storage']['page']);
                !empty($state['storage']['onpage']) && $grid->getStorage()->setOnPage((int) $state['storage']['onpage']);
            }
        }

        //columns
        if (!empty($state['columns'])) {
            foreach ($state['columns'] as $name => $columnState) {
                if ($column = $grid->getColumn($name)) {
                    $column->setHidden((bool) $columnState['hidden']);
                    $column->setPosition((int) $columnState['position']);
                }
            }
        }

        return $this;
    }

}
