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
    public function render()
    {
        $buttons = false;
        $columns = $this->getGrid()->getColumns();
        foreach ($columns as $column) {
            if (!$column->isHidden() && $column->getFilter() && $column->isFilterable()) {
                $buttons = true;
                break;
            }
        }

        if ($buttons) {
            $button = new Button();
            $button->setTitle('Filter');
            $button->setHint('Apply filter');
            $button->setCallback($this->getGrid()->getJavascriptObject() . '.doFilter(); return false;');
            $button->setIcon('filter');
            $this->addButton($button);

            $button = new Button();
            $button->setTitle('Reset');
            $button->setHint('Reset filters');
            $button->setCallback($this->getGrid()->getJavascriptObject() . '.load(\'' . $this->getGrid()->getUrl(array('filter' => false)) . '\'); return false;');
            $button->setIcon('retweet');
            $this->addButton($button);
        }

        return parent::render();
    }
}
