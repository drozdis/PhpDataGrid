<?php
namespace Widget\Grid\Extension;
use Widget\AbstractExtension;
use Widget\Grid\Toolbar\Button;

/**
 * Класc "Експорт таблицы в xls"
 * 
 * @package A1_Widget
 * @author drozd
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
	 * @param unknown_type $config
	 */
	public static function xls($config = array())
	{
		$export = new self($config);
		$export -> export();
	}

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($toolbar = $this->getWidget()->getTopToolbar()) {
            $button = new Button(array(
                'title' => 'Excel'
            ));
            $toolbar->addButton($button);
        }
    }

	/**
	 * Експорт
	 */
	public function export()
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(0);
		
		$data = $this->getGrid()->getStorage()->load(self::MAX)->getData();
		$columns = $this->getGrid()->reorderColumns()->getColumns();
		
		$export = array('data' => array(), 'table' => array());
		
		foreach ($columns as &$column) {
			if ($column->getHidden() == false) {
				$export['table'][] = array('key' => $column->getName(), 'title' => str_replace(array('&nbsp;', '<br/>', '<br>'), array(' ',' ', ' '),$column->getTitle()));
			}
		}
        foreach ($data as &$row) {
			$_row = array();
			foreach ($columns as &$column) {
				if ($column->getHidden() == false) {
	                $value = $column->getValue($row);
	                $m = $this->_getAttribute('excel-value',$value);
	                if ($m === false) {
	                    $m = $this->_getAttribute('value',$value);
	                }
	
	                if ($m !== false) {
	                    $value = $m;
	                }
	
					$_row[$column->getName()] = strip_tags(str_replace(array('&nbsp;', '<br/>', '<br>'), array(' ',' ', ' '), $value));
				}
			}
			$export['data'][] = $_row;
		}
		
		$xml = new A1_Helper_Xls();
		$xml -> setColumns($export['table']);
		$xml -> generate($export['data']);
	}

    /**
     * функция получения значения атрибута html тега
     * @param $attrib
     * @param $tag
     * @return bool|string
     */
    protected function _getAttribute($attrib, $tag)
    {
        //get attribute from html tag
        $re = '/'.$attrib.'=["\']?([^"\' ]*)["\' ]/is';
        preg_match($re, $tag, $match);
        if($match){
            return urldecode($match[1]);
        }else {
            return false;
        }
    }
}