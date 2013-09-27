<?php
namespace Widget\Grid\Toolbar;
use Widget\Helper;

/**
 * Quick Action
 *
 * @package Widget\Grid\Toolbar
 * @author drozd
 */
class Action
{
    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @var string
     */
    protected $_href = '';

    /**
     * @var string
     */
    protected $_handler = '';

    /**
     * @var string
     */
    protected $_question = '';

    /**
     * @var string
     */
    protected $_success = '';

    /**
     * @var string
     */
    protected $_errors = '';

    /**
     * @var array
     */
    protected $_params = array();

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        Helper::setConstructorOptions($this, $options);
    }

    /**
     * @param $action
     * @return object|Action
     * @throws \Exception
     */
    public static function factory($action)
    {
        if (is_array($action)) {
            if (!empty($action['class']) && class_exists($action['class'])) {
                $class = $action['class'];
            } else {
                $class = '\Widget\Grid\Toolbar\Action';
            }
            $action = new $class($action);
        } elseif (is_object($action)) {

        } else {
            throw new \Exception('Invalid action configuration');
        }

        if (!($action instanceof \Widget\Grid\Toolbar\Action)) {
            throw new \Exception('Class of toolbar action should be instanced from \Widget\Grid\Toolbar\Action');
        }

        return $action;
    }

    /**
     * @param string $errors
     */
    public function setErrors($errors)
    {
        $this->_errors = $errors;
    }

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @param string $handler
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     * @param string $href
     */
    public function setHref($href)
    {
        $this->_href = $href;
    }

    /**
     * @return string
     */
    public function getHref($row = null)
    {
        return $this->_href;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->_question = $question;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->_question;
    }

    /**
     * @param string $success
     */
    public function setSuccess($success)
    {
        $this->_success = $success;
    }

    /**
     * @return string
     */
    public function getSuccess()
    {
        return $this->_success;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->_params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        $data = array(
            'title' => $this->getTitle(),
            'href' => $this->getHref(),
            'handler' => $this->getHandler(),
            'question' => $this->getQuestion(),
            'success' => $this->getSuccess(),
            'errors' => $this->getErrors(),
            'params' => $this->getParams(),
        );
        return htmlentities(json_encode(array_filter($data)));
    }

}
