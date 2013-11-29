<?php
namespace Widget\Grid\Filter;

/**
 * Клас фильтра колонки (Дерево)
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class TreeFilter extends SelectFilter
{
    /**
     * @var string
     */
    protected $idField = 'id';

    /**
     * @var string
     */
    protected $titleField = 'name';

    /**
     * @param string $idField
     *
     * @return TreeFilter
     */
    public function setIdField($idField)
    {
        $this->idField = $idField;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * @param string $titleField
     *
     * @return TreeFilter
     */
    public function setTitleField($titleField)
    {
        $this->titleField = $titleField;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleField()
    {
        return $this->titleField;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $column = $this->getColumn()->getName();
        $grid = $this->getGrid();
        $tree = $this->getOptions();

        $html = '<div class="field-100">';
        $html .= '<select class="form-control" name="' . $column . '" onchange="' . $grid->getJavascriptObject() . '.doFilter();">';
        $html .= '<option value=""></option>';
        if ($tree) {
            $html .= $this->createTree($tree->data, 0);
        }
        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @param array $tree
     * @param int   $level
     *
     * @return string
     */
    protected function createTree($tree, $level = 0)
    {
        $html = '';
        foreach ($tree as $child) {
            $style = '';
            if (!empty($child['child'])) {
                $style = 'style="color://000;"';
            }

            $html .= '<option ' . $style . ' ' . ($this->getValue() == $child['data'][$this->idField] ? 'selected="selected"' : '') . ' value="' . $child['data'][$this->idField] . '">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level) . $child['data'][$this->titleField] . '</option>';
            if (!empty($child['child'])) {
                $html .= $this->createTree($child['child'], $level + 1);
            }
        }

        return $html;
    }
}
