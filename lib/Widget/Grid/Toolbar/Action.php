<?php
namespace Widget\Grid\Toolbar;

/**
 * Quick Action
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Action
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $href = '';

    /**
     * @var string
     */
    protected $handler = '';

    /**
     * @var string
     */
    protected $question = '';

    /**
     * @var string
     */
    protected $success = '';

    /**
     * @var string
     */
    protected $errors = '';

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param string $href
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param string $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

}
