<?php
namespace Widget\Grid\Storage;

/**
 * The storage that provide a storing data in array
 *
 * Class ArrayStorage
 * @package Widget\Grid\Storage
 */
class ArrayStorage extends AbstractStorage
{
    /**
     * @inheritdoc
     */
    public function order()
	{
		
	}

    /**
     * @inheritdoc
     */
	public function filter()
	{
		
	}

    /**
     * @inheritdoc
     */
	public function load($limit = null)
	{

	}

    /**
     * @inheritdoc
     */
	public function getCount() 
	{
		return count($this->getData());
	}

    /**
     * @inheritdoc
     */
	public function getTotal()
	{
		return count($this->getData());
	}
}