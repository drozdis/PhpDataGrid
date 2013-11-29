<?php
namespace Widget\Grid\Storage;

/**
 * The storage that provide a loading data dynamically from model
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class ModelStorage extends AbstractStorage
{
    /**
     * @var ModelStorageInterface
     */
    protected $model = null;

    /**
     * Использовать selWhere модели или нет
     * @var Boolean
     */
    protected $where = true;

    /**
     * Установка модели
     * @param ModelStorageInterface $model
     *
     * @return ModelStorage
     */
    public function setModel(ModelStorageInterface $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Получение модели
     * @return ModelStorageInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $where
     *
     * @return ModelStorage
     */
    public function setWhere($where)
    {
        $this->where = $where;

        return $this;
    }

    /**
     * Get field of data that is identifier
     * @return string
     */
    public function getIdField()
    {
        return !empty($this->idField) ? $this->idField : $this->getModel()->getIdField();
    }

    /**
     * {@inheritdoc}
     */
    public function load($limit = null)
    {
        //Генерация события предзагрузки
        $this->fireEvent('before_load', array('storage' => $this));

        $model = $this->getModel();
        $model->selColumns();
        $this->where && $model->selWhere();

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
        foreach ($this->orders as $name => $dir) {
            $arr = explode('.', $name);
            $clearName = array_pop($arr);
            $method = '_order' . preg_replace("#_([\w])#e", "ucfirst('\\1')", ucfirst($clearName));
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $dir);
            } else {
                $this->getModel()->selOrder(array($name => $dir));
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
            $method = '_filter' . preg_replace("#_([\w])#e", "ucfirst('\\1')", ucfirst($filter['name']));
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $filter['value']);
            } else {
                $this->getModel()->filter($filter['field'], $filter['operation'], $filter['value'], $filter['function']);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->getModel()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->model = clone $this->model;
    }
}
