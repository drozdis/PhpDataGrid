<?php
namespace Widget\Grid\Storage;

use Widget\Helper;

/**
 * The storage that provide a loading data dynamically from doctrine repository
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class DoctrineStorage extends AbstractStorage
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository = null;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder = null;

    /**
     * @param \Doctrine\ORM\EntityRepository $repository
     *
     * @return DoctrineStorage
     */
    public function setRepository(\Doctrine\ORM\EntityRepository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|null
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder|null
     */
    public function getQueryBuilder()
    {
        if ($this->queryBuilder == null) {
            $this->setQueryBuilder($this->getRepository()->createQueryBuilder('e'));
        }

        return $this->queryBuilder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return $this
     */
    public function setQueryBuilder(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function load($limit = null)
    {
        //fire before_load event
        $this->fireEvent('before_load', array('storage' => $this));

        $query = $this->getQueryBuilder();

        //filter and ordering
        $this->filter()->order();

        $countQuery  = clone $query;
        $countQuery  = $countQuery->select('count(e)')->getQuery();
        $this->count = $countQuery->getSingleScalarResult();

        //fire load event
        $this->fireEvent('load', array('storage' => $this));

        //limit
        if ($limit) {
            $query->setMaxResults($limit);
        } else {
            $query->setFirstResult(($this->getPage() - 1) * $this->getOnPage());
            $query->setMaxResults($this->getOnPage());
        }

        //get data
        $rows = $query->getQuery()->getResult();

        //set data
        $this->setData($rows);

        //fire after_load event
        $this->fireEvent('after_load', array('storage' => $this, 'data' => $rows));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function order()
    {
        foreach ($this->orders as $name => $dir) {
            $method = 'orderBy' . Helper::normalizeMethod($name);
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $dir);
            } else {
                $this->getQueryBuilder()->orderBy('e.' . Helper::normalizeKey($name), $dir);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter()
    {
        foreach ($this->filters as $filter) {
            $method = 'filter' . Helper::normalizeMethod($filter['name']);
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $filter['value']);
            } else {
                $this->filterQuery($this->getQueryBuilder(), $filter['field'], $filter['operation'], $filter['value'], $filter['function']);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        $query = $this->getQueryBuilder()->select('count(e)')->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->repository = clone $this->repository;
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
}
