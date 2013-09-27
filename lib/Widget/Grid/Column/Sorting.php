<?php
namespace Widget\Grid\Column;

/**
 * Колонка drag'n'drop сортировки
 * @package A1_Widget
 * @author orishko
 */
class Sorting extends Column
{
    /**
     * @var string название модели для передачи в контроллер сортировки
     */
    protected $_model = '';

    /**
     * @var array список айдишников, для которых сортировка отменяется
     */
    protected $_exclude = array();

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @return array
     */
    public function getExclude()
    {
        return $this->_exclude;
    }

    /**
     * @param string $model название модели для передачи в контроллер сортировки
     * @return A1_Widget_Grid_Column_Sorting
     */
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @param array $exclude список айдишников, для которых сортировка отменяется
     * @return A1_Widget_Grid_Column_Sorting
     */
    public function setExclude(array $exclude)
    {
        $this->_exclude = $exclude;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see A1_Widget_Grid_Column::_value()
     */
    protected function _value($row)
    {
        //var_dump($this->getHidden());exit;
        $data = array(
            'value' => $this->_getValueFromRow($row, $this->_dataIndex),
            'colname' => $this->_name,
            'model' => $this->_model,
        );
        // для дерева еще учитваем родительскую категорию
        if ($this->getGrid() instanceof A1_Widget_Grid_Tree) {
            $data += array(
                'parentcol' => $this->getGrid()->getParentField(),
                'parent' => $row[$this->getGrid()->getParentField()],
            );
        }
        $data_str = '';
        foreach ($data as $key => $val) {
            $data_str .= ' data-'.$key.'="'.$val.'"';
        }
        // исключения из сортировки
        return !in_array($row[$this->getGrid()->getStorage()->getIdField()], $this->getExclude()) ?
            '<i class="icon-move sorting_handle"'.$data_str.'></i>'/*.$this->_getValueFromRow($row, $this->_dataIndex)*/ :
            '';
    }
}