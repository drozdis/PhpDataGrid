<?php
namespace Widget\Grid\Storage;

use Widget\Helper;

/**
 * Storage builder
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class StorageBuilder
{
    /**
     * @var \Widget\Grid\Storage\AbstractStorage
     */
    private $storage;

    /**
     * @return \Widget\Grid\Storage\AbstractStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param \Widget\Grid\Storage\AbstractStorage | string
     * @param array $options
     */
    public function __construct($type, $options = array())
    {
        if (is_object($type)) {
            $this->storage = $type;
        } elseif (is_string($type)) {
            if (class_exists($type)) {
                $class = $type;
            } elseif (class_exists('\Widget\Grid\Storage\\' . ucfirst($type) . 'Storage')) {
                $class = '\Widget\Grid\Storage\\' . ucfirst($type) . 'Storage';
            } else {
                throw new \Exception('Unknown class ' . $type);
            }
            $this->storage = new $class();
        } else {
            throw new \Exception('Unknown configuration');
        }

        //apply options
        \Widget\Helper::setConstructorOptions($this->storage, $options);
    }
}
