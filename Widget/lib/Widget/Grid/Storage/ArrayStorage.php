<?php
namespace Widget\Grid\Storage;

/**
 * The storage that provide a storing data in array
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class ArrayStorage extends AbstractStorage
{
    /**
     * {@inheritdoc}
     */
    public function order()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function filter()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function load($limit = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getCount()
    {
        return count($this->getData());
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return count($this->getData());
    }
}
