<?php
namespace Widget\Grid\Extension;
use Widget\AbstractExtension;
use Widget\Grid\Toolbar\Button;
use Widget\Grid\Toolbar\Toolbar;

/**
 * Класc "Експорт таблицы в xls"
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Export extends AbstractExtension
{
    /**
     * Макс к-во строк
     * @var Integer
     */
    const MAX = 5000;

    /**
     * Фактори метод єкспорта в ексель
     *
     * @param array $config
     */
    public static function xls($config = array())
    {
        $export = new self($config);
        $export->getXls();
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($toolbar = $this->getWidget()->getTopToolbar()) {
            $button = new Button();
            $button->setTitle('Excel');
            $toolbar->addButton($button, Toolbar::SERVICE_CONTAINER);
        }
    }

    /**
     * Експорт
     */
    public function getXls()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $data = $this->getGrid()->getStorage()->load(self::MAX)->getData();
        $columns = $this->getGrid()->reorderColumns()->getColumns();

        $export = array('data' => array(), 'table' => array());

        foreach ($columns as &$column) {
            if ($column->isHidden() == false) {
                $export['table'][] = array('key' => $column->getName(), 'title' => str_replace(array('&nbsp;', '<br/>', '<br>'), array(' ', ' ', ' '), $column->getTitle()));
            }
        }
        foreach ($data as &$row) {
            $_row = array();
            foreach ($columns as &$column) {
                if ($column->isHidden() == false) {
                    $value = $column->getValue($row);
                    $m = $this->getAttribute('excel-value', $value);
                    if ($m === false) {
                        $m = $this->getAttribute('value', $value);
                    }

                    if ($m !== false) {
                        $value = $m;
                    }

                    $_row[$column->getName()] = strip_tags(str_replace(array('&nbsp;', '<br/>', '<br>'), array(' ', ' ', ' '), $value));
                }
            }
            $export['data'][] = $_row;
        }

        $xml = new A1_Helper_Xls();
        $xml->setColumns($export['table']);
        $xml->generate($export['data']);
    }

    /**
     * функция получения значения атрибута html тега
     *
     * @param string $attrib
     * @param string $tag
     *
     * @return bool|string
     */
    protected function getAttribute($attrib, $tag)
    {
        //get attribute from html tag
        $re = '/' . $attrib . '=["\']?([^"\' ]*)["\' ]/is';
        preg_match($re, $tag, $match);
        if ($match) {
            return urldecode($match[1]);
        } else {
            return false;
        }
    }
}
