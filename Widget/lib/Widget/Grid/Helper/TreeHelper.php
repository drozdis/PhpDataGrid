<?php
namespace Widget\Grid\Helper;

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
     * @var Integer
     */
    protected $id = 'id';

    /**
     * Идентификатор "ссылка на родилеть"
     * @var Integer
     */
    protected $pid = 'pid';

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
     * @param $row
     *
     * @return mixed
     */
    protected function getId($row)
    {
        return $this->getValue($row, $this->id);
    }

    /**
     * @param array $row
     *
     * @return mixed
     */
    protected function getPid($row)
    {
        return $this->getValue($row, $this->pid);
    }

    /**
     * @param array|object $row
     * @param string       $key
     *
     * @return mixed
     */
    public function getValue($row, $key)
    {
        $mehtod = 'get' . preg_replace("#_([\w])#e", "ucfirst('\\1')", ucfirst($key));

        return call_user_func(array($row, $mehtod));
    }

    /**
     * @param Integer $id
     *
     * @return TreeHelper
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param Integer $pid
     *
     * @return TreeHelper
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return TreeHelper
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return TreeHelper
     */
    public function setActive(array $data)
    {
        $this->active = (array) $data;

        return $this;
    }

    /**
     * setters for counter
     *
     * @param string $counter
     *
     * @return TreeHelper
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * setters for deleteEmpty
     *
     * @param $flag boolean
     */
    public function setDeleteEmpty($flag = true)
    {
        $this->deleteEmpty = (boolean) $flag;

        return $this;
    }

    /**
     * @param integer $parentId
     * @param boolean $rebuild
     *
     * @return stdClass
     */
    public function tree($parentId = self::PARENTID_DEFAULT, $rebuild = false)
    {
        if ($rebuild || empty($this->tree[$parentId])) {
            $this->tree[$parentId] = new \stdClass();
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
                'data' => $row,
                'active' => in_array($this->getId($row), $this->active),
                'count_child' => 0,
                'path' => array($this->getId($row)),
                'child' => array()
            );
            if ($this->counter) {
                $value = $this->getValue($row, $this->counter);
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
            $arr = $this->findChild($data, $this->getId($row));
            $out = array_merge($out, $arr);
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
                    if (!empty($child[$this->id][$this->pid])) {
                        $data = array_merge($data, $this->findChildData($this->data, $child[$this->id][$this->pid]));
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
            $arr = $recurs ? $this->findChildData($data, $this->getId($row), $recurs) : array();
            $out = array_merge($out, $arr);
        }

        return $out;
    }

    /**
     * @param boolean $assoc
     *
     * @return array
     */
    public function getData($assoc = false)
    {
        if ($assoc === false) {
            return $this->data;
        } else {
            return A1_Helper_Array::makeAssoc($this->data, $this->id);
        }
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getItem($id)
    {
        return A1_Helper_Array::find($this->data, $this->id, $id);
    }

    /**
     * получение всех id дерева
     *
     * @return array
     */
    public function getAllID()
    {
        return A1_Helper_Array::findKey($this->id, $this->data);
    }

    /**
     * @param array $way way
     *
     * @return Integer
     */
    public function getBranchByWay(array $way, $parentId = self::PARENTID_DEFAULT, $key = 'code')
    {
        $result = $this->getBranchByWayRecursive($this->tree($parentId)->data, $way, 0, $key);
        if ($result === false) {
            return false;
        } else {
            return $way;
        }
    }

    public function getBranchById($id, $parentId = self::PARENTID_DEFAULT)
    {
        $result = $this->getBranchByIdRecursive(array('way' => array(), 'finished' => false), $id, $this->tree($parentId)->data);
        if ($result['finished'] === false || sizeof($result['way']) === 0) {
            return false;
        } else {
            return $result['way'];
        }
    }

    protected function getBranchByIdRecursive($result, $id, $tree)
    {
        foreach ($tree as $item) {
            $_id = \Widget\Helper::getValue($item['data'], $this->id);

            $localResult = $result;
            $localResult['way'][$_id] = $item['data'];
            $localResult['last_id'] = $_id;
            if ($_id == $id) {
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

    /**
     * search categories in the tree branches
     *
     * @param array   $tree
     * @param array   &$way
     * @param Integer $level
     * @param string  $key
     *
     * @return Integer|Boolean
     */
    protected function getBranchByWayRecursive($tree, array &$way, $level = 0, $key = 'code')
    {
        switch (sizeof($way) - $level) {
            case 0:
                return false;
                break;
            case 1:
                foreach ($tree as $item) {
                    if (@$item['data'][$key] === $way[$level]) {
                        $way[$level] = $item['data'];

                        return floatval($item['data'][$this->id]);
                    }
                }

                return false;
                break;
            default:
                foreach ($tree as $item) {
                    if (@$item['data'][$key] === $way[$level]) {
                        $way[$level] = $item['data'];

                        return $this->getBranchByWayrecursive($item['child'], $way, $level + 1, $key);
                    }
                }

                return false;
                break;
        }
    }
}
