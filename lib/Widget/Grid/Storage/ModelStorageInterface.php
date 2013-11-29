<?php
namespace Widget\Grid\Storage;

/**
 * The storage that provide a loading data dynamically from model
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
interface ModelStorageInterface
{
    /**
     * @param array $columns
     *
     * @return ModelStorageInterface
     */
    public function selColumns($columns = array());

    /**
     * @param array $where
     *
     * @return ModelStorageInterface
     */
    public function selWhere($where = array());

    /**
     * @param array $order
     *
     * @return ModelStorageInterface
     */
    public function selOrder($order = array());

    /**
     * Лимит
     *
     * @param Integer $count
     * @param Integer $offset
     *
     * @return ModelStorageInterface
     */
    public function limit($count, $offset = 0);

    /**
     * Получение к-ва записей
     * @param string $field
     *
     * @return Integer
     */
    public function count($field = null);

    /**
     * Поле которое отвечает Идентификатор
     * @return string
     */
    public function getIdField();

    /**
     * Фильтрация данных
     * <code>
     *  filter('is_active', ' IN(?)', array(1,2,3)); -> is_active IN(1,2,3)
     *  filter('name', ' IN(?)', 'привет', 'LOWER)'); -> LOWER(name) LIKE %привет%
     * </code>
     *
     * @param string $field
     * @param string $operation
     * @param Mixed  $data
     * @param string $function
     *
     * @return ModelStorageInterface
     */
    public function filter($field, $operation, $data, $function = null);

    /**
     * Получение массива записей
     * @param Boolean $assoc
     *
     * @return ArrayObject
     */
    public function findAll($assoc = false);
}
