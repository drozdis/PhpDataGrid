<?php
namespace Widget\Grid\Toolbar;

/**
 * Клас "Тулбар"
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class DefaultToolbar extends Toolbar
{
    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        parent::init();

        $buttons = false;
        $columns = $this->getGrid()->getColumns();
        foreach ($columns as $column) {
            if (!$column->isHidden() && $column->getFilter() && $column->isFilterable()) {
                $buttons = true;
                break;
            }
        }

        if ($buttons) {
            $button = new Button(array('title' => 'Фильтровать', 'hint' => 'Применить фильтры', 'callback' => $this->getGrid()->getJavascriptObject() . '.doFilter(); return false;', 'icon' => 'filter'));
            $this->addButton($button);

            $button = new Button(array('title' => 'Сбросить', 'hint' => 'Сбросить фильтры', 'callback' => $this->getGrid()->getJavascriptObject() . '.load(\'' . $this->getGrid()->getUrl(array('filter' => false)) . '\'); return false;', 'icon' => 'retweet'));
            $this->addButton($button);
        }
    }
}
