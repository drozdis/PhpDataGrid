<?php
namespace Widget\Grid;

use Widget\Grid\Helper\TreeHelper;

/**
 * Class Tree Grid
 * It renders grid as tree
 */
class Tree extends Grid
{
    /**
     * @var string
     */
    protected $idField = 'entity_id';

    /**
     * @var string
     */
    protected $parentField = 'entity_id';

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $options['pagination'] = false;
        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * @param string $idField
     *
     * @return Grid
     */
    public function setIdField($idField)
    {
        $this->idField = $idField;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentField()
    {
        return $this->parentField;
    }

    /**
     * @param string $parentField
     *
     * @return Grid
     */
    public function setParentField($parentField)
    {
        $this->parentField = $parentField;

        return $this;
    }

    /**
     * Рендеринг данных
     * @return string
     */
    protected function renderBody()
    {
        $html    = '';
        $options = array(
            'idField'     => $this->getIdField(),
            'parentField' => $this->getParentField(),
            'data'        => $this->getStorage()->getData()
        );

        $tree = new TreeHelper($options);
        $tree = $tree->tree();
        if (!empty($tree->data)) {
            foreach ($tree->data as $child) {
                $html .= $this->renderChilds($child);
            }
        } else {
            $html .= '<tr><td colspan="' . (count($this->columns) + 2) . '" style="padding: 10px; text-align: center;">Нет данных</td></tr>';
        }

        return $html;
    }

    /**
     * @param array $row
     *
     * @return string
     */
    protected function renderChilds($row)
    {
        $html = $this->renderTr($row['data']);
        if (!empty($row['child'])) {
            foreach ($row['child'] as $child) {
                $html .= $this->renderChilds($child);
            }
        }

        return $html;
    }
}
