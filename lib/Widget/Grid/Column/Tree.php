<?php
namespace Widget\Grid\Column;

/**
 * Колонка дерево
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Tree extends Column
{
    /**
     * {@inheritdoc}
     */
    protected function value($row)
    {
        return str_repeat('&nbsp;', $this->level(\Widget\Helper::getValue($row, $this->getGrid()->getIdField())) * 10) . parent::value($row);
    }

    /**
     * Подсчет уровня вложености
     * @param Integer $id
     *
     * @return Integer $level
     */
    protected function level($id)
    {
        static $tree = null;
        if ($tree === null) {
            $rows    = $this->getGrid()->getStorage()->getData();
            $options = array(
                'idField'     => $this->getGrid()->getIdField(),
                'parentField' => $this->getGrid()->getParentField(),
                'data'        => $rows
            );
            $tree    = new \Widget\Grid\Helper\TreeHelper($options);
        }

        return count($tree->getBranchById($id)) - 1;
    }

}
