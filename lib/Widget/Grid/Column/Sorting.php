<?php
namespace Widget\Grid\Column;

/**
 * Колонка drag'n'drop сортировки
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Sorting extends Column
{
    /**
     * @var string название модели для передачи в контроллер сортировки
     */
    protected $model = '';

    /**
     * @var array список айдишников, для которых сортировка отменяется
     */
    protected $exclude = array();

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * @param string $model название модели для передачи в контроллер сортировки
     *
     * @return Sorting
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param array $exclude список айдишников, для которых сортировка отменяется
     *
     * @return Sorting
     */
    public function setExclude(array $exclude)
    {
        $this->exclude = $exclude;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        $data = array(
            'value' => $this->getValueFromRow($row, $this->dataIndex),
            'colname' => $this->name,
            'model' => $this->model,
        );
        // для дерева еще учитваем родительскую категорию
        if ($this->getGrid() instanceof \Widget\Grid\Tree) {
            $data += array(
                'parentcol' => $this->getGrid()->getParentField(),
                'parent' => $row[$this->getGrid()->getParentField()],
            );
        }
        $data_str = '';
        foreach ($data as $key => $val) {
            $data_str .= ' data-' . $key . '="' . $val . '"';
        }
        // исключения из сортировки
        return !in_array($row[$this->getGrid()->getStorage()->getIdField()], $this->getExclude()) ?
            '<i class="icon-move sorting_handle"' . $data_str . '></i>' : '';
    }
}
