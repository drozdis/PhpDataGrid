<?php
namespace Widget\Grid\Storage;

use Igdr\Bundle\ManagerBundle\Manager\ManagerInterface;
use Widget\Helper;

/**
 * The storage that provide a loading data dynamically from model
 */
class ManagerStorage extends AbstractStorage
{
    /**
     * @var ManagerInterface
     */
    protected $manager = null;

    /**
     * @var Boolean
     */
    protected $where = true;

    /**
     * @param ManagerInterface $manager
     *
     * @return $this
     */
    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param array $where
     *
     * @return $this
     */
    public function setWhere($where)
    {
        $this->where = $where;

        return $this;
    }

    /**
     * Get field of data that is identifier
     *
     * @return string
     */
    public function getIdField()
    {
        return !empty($this->idField) ? $this->idField : $this->getManager()->getIdField();
    }

    /**
     * {@inheritdoc}
     */
    public function load($limit = null)
    {
        //Генерация события предзагрузки
        $this->fireEvent('before_load', array('storage' => $this));

        $model = $this->getManager();
        $this->where && $model->where();

        //сортировка, фильтрация
        $this->filter()->order();

        $this->count = $model->count();

        //Генерация события загрузки данных (применены сортировки, фильтры )
        $this->fireEvent('load', array('storage' => $this));

        //лимит
        if ($limit) {
            $model->limit($limit);
        } else {
            $model->limit($this->getOnPage(), ($this->getPage() - 1) * $this->getOnPage());
        }

        //данные
        $rows = $model->findAll();
        $this->setData($rows);

        //Генерация события послезагрузки
        $this->fireEvent('after_load', array('storage' => $this, 'data' => $rows));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function order()
    {
        $manager = $this->getManager();
        foreach ($this->orders as $name => $dir) {
            $arr       = explode('.', $name);
            $clearName = array_pop($arr);
            $method    = 'order' . Helper::normalizeMethod($clearName);
            if (method_exists($manager, $method)) {
                call_user_func(array($manager, $method), $dir);
            } else {
                $manager->order(array($name => $dir));
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter()
    {
        $manager = $this->getManager();
        foreach ($this->filters as $filter) {
            $method = 'filter' . Helper::normalizeMethod($filter['name']);
            if (method_exists($manager, $method)) {
                call_user_func(array($manager, $method), $filter['value']);
            } else {
                $this->filterQuery($this->getManager()->getQuery(), $filter['field'], $filter['operation'], $filter['value'], $filter['function']);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->getManager()->count();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string                     $field
     * @param string                     $operation
     * @param string                     $data
     *
     * @return $this
     */
    protected function filterQuery(\Doctrine\ORM\QueryBuilder $queryBuilder, $field, $operation, $data)
    {
        static $i = 1;

        if (strpos($field, '.') === false) {
            $field = 'e.' . Helper::normalizeKey($field);
        }
        $var       = 'f' . ($i++);
        $operation = str_replace('?', ':' . $var, $operation);

        $queryBuilder->andWhere($field . ' ' . $operation);
        $queryBuilder->setParameter($var, $data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->manager = clone $this->manager;
    }
}
