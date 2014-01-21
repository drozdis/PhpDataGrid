<?php
namespace Widget\Grid\Helper;

use Widget\Helper;

/**
 * Хелпер для построение деревьев и работы с ними
 */
class TreeHelper
{
    const CHILD_ID = 1;

    const CHILD_ALL = 2;

    const PARENTID_DEFAULT = 0;

    /**
     * Идентификатор данных
     *
     * @var Integer
     */
    protected $idField = 'id';

    /**
     * Идентификатор "ссылка на родилеть"
     *
     * @var Integer
     */
    protected $parentField = 'pid';

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $active = array();

    /**
     * @var Integer
     */
    protected $counter = null;

    /**
     * @var Boolean
     */
    protected $deleteEmpty = false;

    /**
     * @var array
     */
    protected $tree = array();

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        \Widget\Helper::setConstructorOptions($this, $config);
    }

    /**
     * @param array|obejct $row
     *
     * @return mixed
     */
    protected function getId($row)
    {
        return $this->getValue($row, $this->idField);
    }

    /**
     * @param array $row
     *
     * @return mixed
     */
    protected function getPid($row)
    {
        return $this->getValue($row, $this->parentField);
    }

    /**
     * @param array|object $row
     * @param string       $key
     *
     * @return mixed
     */
    public function getValue($row, $key)
    {
        return call_user_func(array($row, 'get' . Helper::normalizeKey($key)));
    }

    /**
     * @param string $idField
     *
     * @return $this
     */
    public function setIdField($idField)
    {
        $this->idField = $idField;

        return $this;
    }

    /**
     * @param string $parentField
     *
     * @return $this
     */
    public function setParentField($parentField)
    {
        $this->parentField = $parentField;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $counter
     *
     * @return $this
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }


    /**
     * @param int  $parentId
     * @param bool $rebuild
     *
     * @return object
     */
    public function tree($parentId = self::PARENTID_DEFAULT, $rebuild = false)
    {
        if ($rebuild || empty($this->tree[$parentId])) {
            $this->tree[$parentId]        = new \stdClass();
            $this->tree[$parentId]->count = 0;

            $this->tree[$parentId]->data = array();
            $this->buildTree($this->data, $this->tree[$parentId]->data, $parentId);
            foreach ($this->tree[$parentId]->data as $row) {
                $this->tree[$parentId]->count += $row['count_child'] + 1;
            }
        }

        return $this->tree[$parentId];
    }

    /**
     * @param array   $data
     * @param array   &$tree
     * @param integer $parentId
     *
     * @return integer
     */
    protected function buildTree($data, &$tree, $parentId = 0)
    {
        $total = 0;
        foreach ($data as $k => &$row) {
            if ($this->getPid($row) != $parentId) {
                continue;
            }
            unset($data[$k]);
            $rec = & $tree[];
            $rec = array(
                'data'        => $row,
                'active'      => in_array($this->getId($row), $this->active),
                'count_child' => 0,
                'path'        => array($this->getId($row)),
                'child'       => array()
            );
            if ($this->counter) {
                $value          = $this->getValue($row, $this->counter);
                $rec['counter'] = !empty($value) ? $value : 0;
            }
            $count = $this->buildTree($data, $rec['child'], $this->getId($row));

            foreach ($rec['child'] as $child) {
                if ($child['active']) {
                    $rec['active'] = true;
                    break;
                }
                if ($this->counter) {
                    $rec['counter'] += !empty($child['counter']) ? $child['counter'] : 0;
                }
            }
            array_unshift($rec['path'], $parentId);
            $rec['count_child'] = $count + count($rec['child']);
            $total += $rec['count_child'];
        }

        return $total;
    }

    /**
     * @param integer $parentId
     * @param integer $mode
     *
     * @return array
     */
    public function child($parentId = 0, $mode = self::CHILD_ID)
    {
        $data = array();
        switch ($mode) {
            case self::CHILD_ID:
            default:
                $data = $this->findChild($this->data, $parentId);
                break;

            case self::CHILD_ALL:
                $child = $this->findChild($this->data, $parentId);
                foreach ($this->data as $row) {
                    if (in_array($this->getId($row), $child)) {
                        $data[] = $row;
                    }
                }
                break;
        }

        return $data;
    }

    /**
     * @param array   $data
     * @param integer $parentId
     *
     * @return array
     */
    protected function findChild($data, $parentId = 0)
    {
        $out = array();
        foreach ($data as $k => $row) {
            if ($this->getPid($row) != $parentId) {
                continue;
            }
            unset($data[$k]);
            $out[] = $this->getId($row);
            $arr   = $this->findChild($data, $this->getId($row));
            $out   = array_merge($out, $arr);
        }

        return $out;
    }

    /**
     * @param integer $parentId
     * @param integer $mode
     *
     * @return array
     */
    public function childData($parentId = 0, $mode = self::CHILD_ID)
    {
        $data = array();
        switch ($mode) {
            case self::CHILD_ID:
            default:
                $data = $this->findChildData($this->data, $parentId, false);
                break;

            case self::CHILD_ALL:
                $data = $childs = $this->findChildData($this->data, $parentId, true);
                foreach ($childs as $child) {
                    if (!empty($child[$this->idField][$this->parentField])) {
                        $data = array_merge($data, $this->findChildData($this->data, $child[$this->idField][$this->parentField]));
                    }
                }
                break;
        }

        return $data;
    }

    /**
     * @param array   $data
     * @param integer $parentId
     * @param boolean $recurs
     *
     * @return array
     */
    protected function findChildData($data, $parentId = 0, $recurs = false)
    {
        $out = array();
        foreach ($data as $k => $row) {
            if ($this->getPid($row) != $parentId) {
                continue;
            }
            unset($data[$k]);
            $out[] = $row;
            $arr   = $recurs ? $this->findChildData($data, $this->getId($row), $recurs) : array();
            $out   = array_merge($out, $arr);
        }

        return $out;
    }


    /**
     * @param int $id
     * @param int $parentId
     *
     * @return array|bool
     */
    public function getBranchById($id, $parentId = self::PARENTID_DEFAULT)
    {
        $result = $this->getBranchByIdRecursive(array('way' => array(), 'finished' => false), $id, $this->tree($parentId)->data);
        if ($result['finished'] === false || sizeof($result['way']) === 0) {
            return false;
        } else {
            return $result['way'];
        }
    }

    /**
     * @param array $result
     * @param int   $id
     * @param int   $tree
     *
     * @return array
     */
    protected function getBranchByIdRecursive($result, $id, $tree)
    {
        foreach ($tree as $item) {
            $findId = \Widget\Helper::getValue($item['data'], $this->idField);

            $localResult                 = $result;
            $localResult['way'][$findId] = $item['data'];
            $localResult['last_id']      = $findId;
            if ($findId == $id) {
                $localResult['finished'] = true;

                return $localResult;
            }
            if (sizeof($item['child']) > 0) {
                $localResult = $this->getBranchByIdRecursive($localResult, $id, $item['child']);
                if ($localResult['finished'] === true) {
                    return $localResult;
                }
            }
        }

        return $result;
    }

}
