<?php
namespace Widget\Grid\Extension;
use Widget\AbstractExtension;
use Widget\Grid\Toolbar\Button;

/**
 * @package Widget\Grid\Extension
 * @author drozd
 */
class Columns extends AbstractExtension
{				
	/**
	 * Инициализация плагина
	 */
	public function init()
	{
        if ($toolbar = $this->getWidget()->getTopToolbar()) {
            $button = new Button(array(
                'hint'  => 'Настройка колонок таблицы',
                'href'  => '#grid-extension-columns',
                'icon'  => 'th-list'
            ));
            $toolbar->addButton($button);

            $this->getWidget()->addDecorator(new ColumnsDecorator());
        }
	}
}