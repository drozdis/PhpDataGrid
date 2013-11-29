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
            $toolbar->addButton($button);

            $this->getWidget()->addDecorator(new ColumnsDecorator());
        }
    }
}
