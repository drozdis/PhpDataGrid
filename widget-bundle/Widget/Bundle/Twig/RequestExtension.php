<?php
namespace Widget\Bundle\Twig;

use Widget\Helper;

/**
 * Class RequestExtension
 */
class RequestExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('request', array($this, 'createRequest')),
        );
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function createRequest(array $params)
    {
        return Helper::url($params);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'grid_request_extension';
    }
}