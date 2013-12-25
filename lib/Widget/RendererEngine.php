<?php
namespace Widget;

/**
 * Class AbstractRendererEngine
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class RendererEngine
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * constructor
     */
    public function __construct()
    {
        $path       = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Grid' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'views';
        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem($path));
    }

    /**
     * @param string $template
     * @param array  $params
     *
     * @return string
     */
    public function render($template, $params = array())
    {
        return $this->twig->loadTemplate($template)->renderBlock('element', $params);
    }
}
