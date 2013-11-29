<?php
namespace Widget\Grid\Column;

/**
 * Phone column
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Phone extends Column
{
    /**
     * @var string
     */
    private $format = '(###) ###-##-##';

    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        return $this->formatPhone(parent::value($row));
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param array  $phone
     * @param string $format
     *
     * @return string
     */
    private function formatPhone($phone, $format = null)
    {
        if (empty($phone)) {
            return $phone;
        }
        $phone = preg_replace('#[^\d]#', '', $phone);
        $phone = preg_replace('#^\+?3\s?8#', '', $phone);
        $result = $format ? $format : $this->format;
        $i = 0;
        while (($ps = strpos($result, '#')) !== false) {
            $result = substr_replace($result, isset($phone[$i]) ? $phone[$i] : '', $ps, 1);
            $i++;
        }

        return $result;
    }
}
