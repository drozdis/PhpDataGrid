<?php
namespace Widget;

/**
 * Observer Event
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class ObserverListener
{
    /**
     * @var callback
     */
    protected $method = null;

    /**
     * @param callback
     */
    public function __construct($method)
    {
        $this->method = $method;
    }

    /**
     * @return callback
     */
    public function getMethod()
    {
        return $this->method;
    }
}
