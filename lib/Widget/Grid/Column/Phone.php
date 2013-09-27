<?php
namespace Widget\Grid\Column;

/**
 * Колонка телефон
 * @package Widget\Grid\Column
 * @author drozd
 */
class Phone extends Column
{
    /**
     * @var string
     */
    protected $_format = '(###) ###-##-##';

	/**
	 * @inheritdoc
	 */
    protected function _value($row)
    {  	
    	return $this->_phone(parent::_value($row));
    }

    /**
     * @param Array $options
     * @return String
     */
    protected function _phone($phone, $format = null)
    {
        if (empty($phone)) {
            return $phone;
        }
        $phone = preg_replace('#[^\d]#', '', $phone);
        $phone = preg_replace('#^\+?3\s?8#', '', $phone);
        $result = $format ? $format : $this->_format;
        $i = 0;
        while (($ps = strpos($result, '#')) !== false) {
            $result = substr_replace($result, isset($phone[$i]) ? $phone[$i] : '' , $ps, 1);
            $i++;
        }
        return $result;
    }
}