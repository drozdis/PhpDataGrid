<?php
namespace Widget\Grid\Extension;

use Widget\AbstractExtension;
use Widget\Grid\Toolbar\Button;
use Widget\Grid\Toolbar\Toolbar;
use Widget\ObserverListener;

/**
 * Grid extension for show/hide/reorder columns
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Columns extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->render();

        $columns = $this->getWidget()->getUrlParams('extension-columns');
        if (!empty($columns)) {
            $this->apply($columns);
        }
    }

    /**
     * Render extension
     */
    private function render()
    {
        $grid = $this->getWidget();

        if ($toolbar = $grid->getTopToolbar()) {
            $button = new Button();
            $button->setHint('Configure columns');
            $button->setIcon('th-list');
            $button->setCallback("$('#grid-extension-columns').modal()");
            $toolbar->addButton($button, Toolbar::SERVICE_CONTAINER);

            $listener = new ObserverListener(function ($grid, &$content) {
                $window = new ColumnsRenderer($grid);
                $content .= $window->render();
            });
            $grid->addEventListener('after_render', $listener);
        }
    }

    /**
     * @param array $columns
     */
    private function apply($columns)
    {
        $grid = $this->getWidget();

        if (!empty($columns['columns'])) {
            foreach ($columns['columns'] as $i => $name) {
                $name = str_replace('col-', '', $name);
                if ($column = $grid->getColumn($name)) {
                    $column->setHidden(false)->setPosition($i + 1);
                }
            }
        }

        if (!empty($columns['disabled'])) {
            foreach ($columns['disabled'] as $j => $name) {
                $name = str_replace('col-', '', $name);
                if ($column = $grid->getColumn($name)) {
                    $column->setHidden(true)->setPosition($j + count($columns['columns']) + 1);
                }
            }
        }

        if (!empty($columns['clear'])) {
            $i = 1;
            foreach ($this->getColumns() as $column) {
                $column->setPosition($i++)->setHidden($column->isHidden());
            }
        }
    }
}
