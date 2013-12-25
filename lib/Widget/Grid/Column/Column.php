<?php
namespace Widget\Grid\Column;

use Widget\AbstractRenderer;
use Widget\Grid\Filter\AbstractFilter;
use Widget\Grid\Grid;
use Widget\Helper;

/**
 * Grid column
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Column extends AbstractRenderer
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * Заголовок
     * @var string
     */
    protected $title = '';

    /**
     * Подсказка
     * @var string
     */
    protected $hint = '';

    /**
     * Ширина
     * @var string
     */
    protected $width = '';

    /**
     * Позиционирования (left, center, right)
     *
     * @var string
     */
    protected $align = '';

    /**
     * @var boolean
     */
    protected $nowrap = false;

    /**
     * Скрыть/Паказать колонку
     * @var Boolean
     */
    protected $hidden = false;

    /**
     * Вкл/Выкл сортировки
     * @var string
     */
    protected $sortable = false;

    /**
     * Позиция
     * @var string
     */
    protected $position = null;

    /**
     * Ссылка
     * @var string|array
     */
    protected $url = '';

    /**
     * Путь к данным
     * Берем данные по полю не с $row[$name], а с масива, например $row['category['name при dataIndex = category.name
     *
     * @var string
     */
    protected $dataIndex = null;

    /**
     * Поле в БД name -> sp.name
     *
     * @var string
     */
    protected $field = null;

    /**
     * @var AbstractFilter
     */
    protected $filter = null;

    /**
     * Включен/Выключен фильтр
     * @var Boolean
     */
    protected $filterable = true;

    /**
     * @var Grid
     */
    protected $grid = null;

    /**
     * Начальные настройки колонки
     * @var array
     */
    protected $options = array();

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Column/column.html.twig';
    }

    /**
     * @param string $name
     *
     * @return Column
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @return Integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return string
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * @param Integer $position
     *
     * @return Column
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Установка переноса строк
     *
     * @param Boolean $wrap
     *
     * @return Column
     */
    public function setNowrap($wrap)
    {
        $this->nowrap = $wrap;

        return $this;
    }

    /**
     * @return Boolean
     */
    public function isNowrap()
    {
        return $this->nowrap;
    }

    /**
     * @return Boolean
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @return Boolean
     */
    public function isFilterable()
    {
        return $this->filterable;
    }

    /**
     * @return string
     */
    public function getDataIndex()
    {
        return $this->dataIndex;
    }

    /**
     * @param string $title
     *
     * @return Column
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $hint
     *
     * @return Column
     */
    public function setHint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * @param string $width
     *
     * @return Column
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @param string $align
     *
     * @return Column
     */
    public function setAlign($align)
    {
        $this->align = $align;

        return $this;
    }

    /**
     * @param string $sortable
     *
     * @return Column
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @param string $filterable
     *
     * @return Column
     */
    public function setFilterable($filterable)
    {
        $this->filterable = $filterable;

        return $this;
    }

    /**
     * @param string $dataIndex
     *
     * @return Column
     */
    public function setDataIndex($dataIndex)
    {
        $this->dataIndex = $dataIndex;

        return $this;
    }

    /**
     * @param Boolean $hidden
     *
     * @return Column
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @return Boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field ? $this->field : $this->name;
    }

    /**
     * @param string $field
     *
     * @return Column
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @param AbstractFilter $filter
     *
     * @return Column
     */
    public function setFilter(AbstractFilter $filter)
    {
        //сохраняем
        $this->filter = $filter;
        $this->filter->setColumn($this);

        return $this;
    }

    /**
     * @return Column
     */
    public function removeFilter()
    {
        $this->filter = null;

        return $this;
    }

    /**
     * @return AbstractFilter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param Grid $grid
     *
     * @return Column
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * <code>
     *  setUrl('/shop/product/{{id}}')
     *  setUrl(array(
     *        'href' => '/shop/product/{{id}}',
     *        'target => 'blank'
     *  ))
     *
     * @param string $url
     *
     * @return Column
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get value of column
     *
     * @return string
     */
    public function getValue()
    {
        //get value
        $value = $this->getValueFromRow($this->getData(), $this->dataIndex);

        //render link
        if ($url = $this->getUrl()) {
            $href   = is_array($url) ? $url['href'] : $url;
            $target = is_array($url) && !empty($url['target']) ? $url['target'] : '';

            $href = $href . (strpos($href, '?') === false ? '?' : '&') . 'return=' . urlencode($this->getGrid()->getUrl());
            //замена конструкций {{param}} на значение
            if (preg_match_all('//{{([\d\w_]+)}}//', $href, $m)) {
                foreach ($m[1] as $key) {
                    $href = str_replace('{{' . $key . '}}', isset($row[$key]) ? $row[$key] : '', $href);
                }
            }
            $value = '<a ' . ($target ? 'target="' . $target . '"' : '') . ' href="' . $href . '">' . $value . '</a>';
        }

        return $value;
    }

    /**
     * Get value of column
     *
     * @param array|object $row
     *
     * @return string
     */
    protected function getValueFromRow($row, $key)
    {
        if (!empty($key)) {
            $dataIndex = explode('.', $key);
            $value     = $row;
            foreach ($dataIndex as $index) {
                if (empty($value)) {
                    break;
                }

                $v = \Widget\Helper::getValue($value, $index);
                if ($v !== null) {
                    $value = $v;
                } else {
                    $value = '';
//                    if (array_key_exists($index, $value) && is_null($value[$index])) {
//                        $value = '';
//                    } else {
//                        $value = join('<br/>', A1_Helper_array::findKey($index, $value));
//                    }
                }
            }
            if (is_array($value)) {
                $value = join('<br/>', $value);
            }
        } else {
            $value = Helper::getValue($row, $this->name);
        }

        if (is_array($value) && empty($value)) {
            $value = '';
        }

        return $value;
    }
}
