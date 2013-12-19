<?php
namespace Widget\Grid\Extension;
use Widget\AbstractExtension;
use Widget\Grid\Toolbar\Button;

/**
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Columns extends AbstractExtension
{
    /**
     * Инициализация плагина
     */
    public function init()
    {
        if ($toolbar = $this->getWidget()->getTopToolbar()) {
            $button = new Button();
            $button->setHint('Настройка колонок таблицы');
            $button->setIcon('th-list');
            $button->setCallback("$('#grid-extension-columns').modal()");
            $toolbar->addButton($button);

            $this->getWidget()->addDecorator(new ColumnsDecorator());
        }

        //        // ????????? drag'n'drop ???? ???? ?????????? ?? ???????
//        foreach ($this->getColumns() as $column) {
//            if ($column instanceof \Widget\Grid\Column\Sorting) {
//                $column->setHidden(!empty($order));
//            }
//        }
//
//        //???/???? ???????
//        $extensionColumns = $this->getUrlParams('extension-columns', false);
//        if (!empty($extensionColumns['columns'])) {
//            foreach ($extensionColumns['columns'] as $i => $name) {
//                $name = str_replace('col-', '', $name);
//                if ($column = $this->getColumn($name)) {
//                    $column->setHidden(false)->setPosition($i + 1);
//                }
//            }
//        }
//        if (!empty($extensionColumns['disabled'])) {
//            foreach ($extensionColumns['disabled'] as $j => $name) {
//                $name = str_replace('col-', '', $name);
//                if ($column = $this->getColumn($name)) {
//                    $column->setHidden(true)->setPosition($j + count($extensionColumns['columns']) + 1);
//                }
//            }
//        }
//        if (!empty($extensionColumns['clear'])) {
//            $i = 1;
//            foreach ($this->getColumns() as $column) {
//                $column->setPosition($i++)->setHidden($column->isHidden());
//            }
//        }

    }
}
